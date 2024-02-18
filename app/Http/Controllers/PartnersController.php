<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartnerResource as Res;
use App\Models\Partner as TableName;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnersController extends Controller
{
    public function index(Request $request)
    {
        $data = TableName::query();
        if ($request->input('type')) {
            $data->where('type', $request->type);
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('phone', 'LIKE', '%' . $request->search . '%')
                ->orWhere('contact_name', 'LIKE', '%' . $request->search . '%');
            });
        }
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'contact_name' => 'sometimes|string',
            'phone' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'type', 'contact_name', 'phone']);

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
            'name' => 'sometimes|string',
            'type' => 'sometimes|string',
            'contact_name' => 'sometimes|string',
            'phone' => 'sometimes',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'type', 'contact_name', 'phone']);

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
