<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource as Res;
use App\Models\ActivitiesLog;
use App\Models\Payments as TableName;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    public function index($id, Request $request)
    {
        $data = TableName::query()->where('order_id', $id);
        if ($request->input('paid_at')) {
            $data->where('paid_at', $request->paid_at);
        }
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'paid_at' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }

        $data = $request->only([
            'order_id',
            'amount',
            'currency',
            'paid_at',
            'note',
            'type',
        ]);

        $data['paid_at'] = $request->input('paid_at', date('Y-m-d'));

        $newRow = TableName::create($data);

        $data = TableName::find($newRow->id, [
            'amount',
            'currency',
            'paid_at',
            'note',
            'type',
        ])->toArray();

        ActivitiesLog::create([
            'user_id' => $request->user()->id,
            'order_id' => $newRow->order_id,
            'item_id' => $newRow->id,
            'item_type' => TableName::class,
            'data' => $data,
            'type' => 'Add Payment',
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
            'amount' => 'required',
            'currency' => 'required',
            'paid_at' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'amount',
            'currency',
            'paid_at',
            'note',
            'type',
        ]);
        $data['paid_at'] = Carbon::parse($data['paid_at'])->format('Y-m-d');

        $itemBeforeUpdate = TableName::where('id', $id)->select([
            'amount',
            'currency',
            'paid_at',
            'note',
            'type',
        ])->first()->toArray();
        $newUpdates = Helper::arrayDiffValues($data, $itemBeforeUpdate);
        
        $item->update($data);
        
        if (count($newUpdates) > 0) {
            ActivitiesLog::create([
                'user_id' => $request->user()->id,
                'order_id' => $item->order_id,
                'item_id' => $item->id,
                'item_type' => TableName::class,
                'data' => $newUpdates,
                'type' => 'Update Payment',
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
