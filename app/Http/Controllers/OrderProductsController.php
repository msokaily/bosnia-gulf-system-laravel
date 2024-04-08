<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderProductResource as Res;
use App\Models\Accommodation;
use App\Models\ActivitiesLog;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderProducts as TableName;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderProductsController extends Controller
{
    public function index($id, Request $request)
    {
        $data = TableName::query()->where('order_id', $id);
        if ($request->input('type')) {
            $data->where('type', $request->type);
        }
        if ($request->input('search')) {
            $data->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('id', 'LIKE', '%' . $request->search . '%')->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }
        return $this->resJson(Res::collection($data->get()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'item_id' => 'required',
            'order_id' => 'required',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Create failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'type',
            'item_id',
            'order_id',
            'start_at',
            'end_at',
            'note',
            'extra',
        ]);

        if ($request->type == 'car') {
            $product = Car::find($request->item_id);
        } else if ($request->type == 'driver') {
            $product = Driver::find($request->item_id);
        } else {
            $product = Accommodation::find($request->item_id);
        }

        $data['cost'] = $product->cost;
        $data['price'] = $request->input('price', 0) > 0 ? $request->price : $product->price;
        $start_at = Carbon::parse($data['start_at']);
        $end_at = Carbon::parse($data['end_at']);
        $daysNum = $end_at->diffInDays($start_at);
        $data['total'] = $data['price'] * $daysNum;

        $newRow = TableName::create($data);

        Order::find($newRow->order_id)->calcTotals();

        $item = TableName::find($newRow->id);
        $data = [
            'name' => $item->product->name,
            'type' => $item->product->type ?? $item->type,
            'price' => $item->price,
            'start_at' => Carbon::parse($item->start_at)->format('Y-m-d'),
            'end_at' => Carbon::parse($item->end_at)->format('Y-m-d'),
            'note' => $item->note,
        ];
        if ($item->extra) {
            $data['vehicle'] = $item->extraValue->name ?? null;
        }

        ActivitiesLog::create([
            'user_id' => $request->user()->id,
            'order_id' => $item->order_id,
            'item_id' => $newRow->id,
            'item_type' => TableName::class,
            'data' => $data,
            'type' => 'Add '.($item->product->type ?? $item->type),
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
            'type' => 'required',
            'item_id' => 'required',
            'order_id' => 'required',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->resJson([
                'message' => 'Update failed!',
                'errors' => Helper::errorsFormat($validator->errors()->toArray())
            ], false);
        }
        $data = $request->only([
            'type',
            'item_id',
            'order_id',
            'start_at',
            'end_at',
            'note',
            'extra',
        ]);
        
        if ($request->type == 'car') {
            $product = Car::find($request->item_id);
        } else if ($request->type == 'driver') {
            $product = Driver::find($request->item_id);
        } else {
            $product = Accommodation::find($request->item_id);
        }

        $data['cost'] = $product->cost;
        $data['price'] = $request->input('price', 0) > 0 ? $request->price : $product->price;
        $start_at = Carbon::parse($data['start_at']);
        $end_at = Carbon::parse($data['end_at']);
        $daysNum = $end_at->diffInDays($start_at);
        $data['total'] = $data['price'] * $daysNum;

        $itemBeforeUpdate = TableName::find($id);
        $itemBeforeUpdateData = [
            'name' => $itemBeforeUpdate->product->name,
            'type' => $itemBeforeUpdate->product->type,
            'price' => $itemBeforeUpdate->price,
            'start_at' => Carbon::parse($itemBeforeUpdate->start_at)->format('Y-m-d'),
            'end_at' => Carbon::parse($itemBeforeUpdate->end_at)->format('Y-m-d'),
            'note' => $itemBeforeUpdate->note,
            'vehicle' => $itemBeforeUpdate->extra ? $itemBeforeUpdate->extraValue->name : null,
        ];
        
        $item->update($data);
        Order::find($item->order_id)->calcTotals();
        
        $itemAfterUpdate = TableName::find($id);
        $itemAfterUpdateData = [
            'name' => $itemAfterUpdate->product->name,
            'type' => $itemAfterUpdate->product->type,
            'price' => $itemAfterUpdate->price,
            'start_at' => Carbon::parse($itemAfterUpdate->start_at)->format('Y-m-d'),
            'end_at' => Carbon::parse($itemAfterUpdate->end_at)->format('Y-m-d'),
            'note' => $itemAfterUpdate->note,
            'vehicle' => $itemAfterUpdate->extra ? $itemAfterUpdate->extraValue->name : null,
        ];

        $itemUpdated = Helper::arrayDiffValues($itemAfterUpdateData, $itemBeforeUpdateData);

        if (count($itemUpdated) > 0) {
            ActivitiesLog::create([
                'user_id' => $request->user()->id,
                'order_id' => $item->order_id,
                'item_id' => $item->id,
                'item_type' => TableName::class,
                'data' => $itemUpdated,
                'type' => 'Update '.$item->product->type,
            ]);
        }

        return $this->resJson([
            'message' => 'Updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $item = TableName::findOrFail($id);
        $order_id = $item->order_id;
        $item->delete();
        Order::find($order_id)->calcTotals();
        return $this->resJson([
            'message' => 'Deleted successfully!'
        ]);
    }

    public function show($id)
    {
        return $this->resJson(new Res(TableName::find($id)));
    }
}
