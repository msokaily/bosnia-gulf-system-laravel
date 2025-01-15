<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarResource as Res;
use App\Models\Car as TableName;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarsController extends Controller
{
    public function index(Request $request)
    {
        $data = TableName::query();
        if ($request->input('company')) {
            $data->whereIn('company', json_decode($request->company));
        }
        if ($request->input('status')) {
            $data->where('status', $request->status);
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('model', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->input('register_end')) {
            $data->whereDate('register_end', '<=', $request->register_end);
        }

        if ($request->input('from') && $request->input('to')) {
            return $this->resJson($request->all(), false);
            $data->whereDoesntHave('active_reservations', function ($q) use ($request) {
                $q->whereDate('start_at', '<=', $request->to)
                    ->whereDate('end_at', '>=', $request->from);
            });
        }
        
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'model' => 'sometimes',
            'company' => 'required',
            'register_no' => 'sometimes',
            'register_start' => 'sometimes',
            'register_end' => 'sometimes',
            'register_no' => 'sometimes',
            'owner' => 'sometimes',
            'owner_id' => 'sometimes',
            'cost' => 'required',
            'price' => 'required',
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'model', 'company', 'register_no', 'register_start', 'register_end', 'owner', 'owner_id', 'cost', 'price', 'image', 'status']);
        
        $newRow = TableName::create($data);

        return $this->resJson(new Res($newRow));
    }
    
    public function update($id, Request $request)
    {
        $item = TableName::find($id);
        if (!$item) {
            return $this->resJson([
                'message' => 'Not found!',
            ], false);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes',
            'model' => 'sometimes',
            'company' => 'sometimes',
            'register_no' => 'sometimes',
            'register_start' => 'sometimes',
            'register_end' => 'sometimes',
            'register_no' => 'sometimes',
            'owner' => 'sometimes',
            'owner_id' => 'sometimes',
            'cost' => 'sometimes',
            'price' => 'sometimes',
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'model', 'company', 'register_no', 'register_start', 'register_end', 'owner', 'owner_id', 'cost', 'price', 'image', 'status']);
        
        $item->update($data);

        return $this->resJson([
            'message' => 'Updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $item = TableName::findOrFail($id);
        $item->delete();
        return $this->resJson([
            'message' => 'Deleted successfully!'
        ]);
    }

    public function show($id)
    {
        return $this->resJson(new Res(TableName::find($id)));
    }
}
