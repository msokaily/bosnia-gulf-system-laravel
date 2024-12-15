<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Incorrect email or password',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->status != 1) {
            return $this->resJson([
                'message' => 'Your account has been suspended by the admin!'
            ], false);
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->resJson([
                'message' => 'Incorrect email or password!'
            ], false);
        }
        return $this->resJson(new AuthResource($user));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Registration failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'email', 'password']);
        
        $user = User::create($data);

        return $this->resJson(new AuthResource($user));
    }
}
