<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User as TableName;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $data = TableName::query();
        if ($request->input('role')) {
            $data->whereIn('role', json_decode($request->role));
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('email', 'LIKE', '%' . $request->search . '%')->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }
        return $this->resJson(UserResource::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'sometimes',
            'password' => 'required',
            'role' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create user failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'email', 'phone', 'password', 'role']);

        $newUser = TableName::create($data);
        $user = TableName::find($newUser->id);

        return $this->resJson(new UserResource($user));
    }

    public function update($id, Request $request)
    {
        $user = TableName::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'email' => "sometimes|required|email|unique:users,email," . $user->id,
            'phone' => 'sometimes',
            'password' => 'sometimes|string',
            'role' => 'sometimes|string',
            'status' => 'sometimes|in:1,2',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update user failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'email', 'phone', 'password', 'role', 'status']);

        $push_token = $request->input('push_token', null);
        if ($push_token) {
            $prevTokens = $user->push_token ?? [];
            if (!in_array($push_token, $prevTokens) && $push_token != "undefined") {
                $prevTokens[] = $push_token;
                $data['push_token'] = $prevTokens;
            }
        }

        $user->update($data);

        return $this->resJson([
            'message' => 'Updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $user = TableName::findOrFail($id);
        $user->delete();
        return $this->resJson([
            'message' => 'Deleted successfully!'
        ]);
    }

    public function show($id)
    {
        return $this->resJson(new UserResource(TableName::find($id)));
    }

    public function profile(Request $request)
    {
        return $this->resJson(new UserResource($request->user()));
    }

    public function logout(Request $request)
    {
        $push_token = $request->input('push_token', null);
        if ($push_token) {
            $push_tokens = $request->user()->push_token;
            if (in_array($push_token, $push_tokens)) {
                $index = array_search($push_token, $push_tokens);
                array_splice($push_tokens, $index, 1);
                $request->user()->update(['push_token' => $push_tokens]);
            }
        }
        return $this->resJson([
            'message' => 'Logged out successfully!'
        ]);
    }
}
