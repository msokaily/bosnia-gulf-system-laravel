<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource as Res;
use App\Models\ActivitiesLog;
use App\Models\Order as TableName;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $dateFieldName = 'arrive_at';
        $data = TableName::query();
        if ($request->input('status')) {
            $data->whereIn('status', json_decode($request->status));
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('id', 'LIKE', '%' . $request->search . '%')->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $data->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'arrive_time',
            'airline',
            'cost',
            'total',
            'status'
        ]);

        $data['user_id'] = $request->user()->id;

        $newRow = TableName::create($data);

        $data = TableName::find($newRow->id, [
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'arrive_time',
            'status',
            'airline',
        ])->toArray();

        ActivitiesLog::create([
            'user_id' => $request->user()->id,
            'order_id' => $newRow->id,
            'item_id' => $newRow->id,
            'item_type' => TableName::class,
            'data' => $data,
            'type' => 'Add Reservation',
        ]);

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
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }

        $data = $request->only([
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'arrive_time',
            'airline',
            'cost',
            'total',
            'status'
        ]);

        $itemBeforeUpdate = TableName::where('id', $id)->select([
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'arrive_time',
            'airline',
            'status'
        ])->first()->toArray();
        $newUpdates = Helper::arrayDiffValues($data, $itemBeforeUpdate);
        
        $item->update($data);
        
        if (count($newUpdates) > 0) {
            if (isset($newUpdates['status'])) {
                $newUpdates['status'] = Helper::orderStatusName($newUpdates['status']) ? Helper::orderStatusName($newUpdates['status'])['en'] : $newUpdates['status'];
            }
            ActivitiesLog::create([
                'user_id' => $request->user()->id,
                'order_id' => $item->id,
                'item_id' => $item->id,
                'item_type' => TableName::class,
                'data' => $newUpdates,
                'type' => 'Update Reservation',
            ]);
        }

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