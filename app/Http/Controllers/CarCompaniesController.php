<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarCompaniesResource as Res;
use App\Models\CarCompanies as TableName;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarCompaniesController extends Controller
{
    public function index(Request $request)
    {
        $data = TableName::query();
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'image']);
        
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
            'image' => "sometimes|image|mimes:jpg,jpeg,bmp,png"
        ]);
        if ($validator->fails())
        {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only(['name', 'image']);
        
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
