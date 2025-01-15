<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccommodationResource as Res;
use App\Models\Accommodation as TableName;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccommodationsController extends Controller
{
    public function index(Request $request)
    {
        $data = TableName::query();
        if ($request->input('type')) {
            $data->where('type', $request->type);
        }
        if ($request->input('partner_id')) {
            $data->where('partner_id', $request->partner_id);
        }
        if ($request->input('status')) {
            $data->where('status', $request->status);
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('location', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->input('from')) {
            $data->whereHas('active_reservations', function ($q) use ($request) {
                $q->whereDate('start_at', '<=', $request->from);
            });
        }
        if ($request->input('to')) {
            $data->whereHas('active_reservations', function ($q) use ($request) {
                $q->whereDate('end_at', '>=', $request->to);
            });
        }
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'partner_id' => 'sometimes',
            'type' => 'required',
            'cost' => 'sometimes',
            'price' => 'required',
            'address' => 'sometimes',
            'location' => 'sometimes',
            'status' => 'sometimes',
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'name',
            'partner_id',
            'type',
            'cost',
            'price',
            'address',
            'location',
            'image',
            'status'
        ]);

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
            'partner_id' => 'sometimes',
            'type' => 'sometimes',
            'cost' => 'sometimes',
            'price' => 'required',
            'address' => 'sometimes',
            'location' => 'sometimes',
            'status' => 'sometimes',
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'name',
            'ownership',
            'partner_id',
            'type',
            'cost',
            'price',
            'address',
            'location',
            'image',
            'status'
        ]);

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
