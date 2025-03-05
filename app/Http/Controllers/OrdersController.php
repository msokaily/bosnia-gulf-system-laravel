<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource as Res;
use App\Models\ActivitiesLog;
use App\Models\ExtraService;
use App\Models\Order as TableName;
use App\Models\User;
use App\Services\Notifications;
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
        $selectedDate = Carbon::parse("$year-$month-01");
        if ($request->has('expand_months')) {
            $data->where(function($q) use ($dateFieldName, $selectedDate) {
                $q->where(function($q1) use ($dateFieldName, $selectedDate) {
                    $q1->whereYear($dateFieldName, $selectedDate->year)->whereMonth($dateFieldName, $selectedDate->month);
                })->orWhere(function($q2) use ($dateFieldName, $selectedDate) {
                    $q2->whereYear($dateFieldName, $selectedDate->copy()->addMonth()->year)->whereMonth($dateFieldName, $selectedDate->copy()->addMonth()->month);
                })->orWhere(function($q2) use ($dateFieldName, $selectedDate) {
                    $q2->whereYear($dateFieldName, $selectedDate->copy()->subMonth()->year)->whereMonth($dateFieldName, $selectedDate->copy()->subMonth()->month);
                });
            });
        } else {
            $data->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        }

        if ($request->input('accommodation_ids'))
        {
            $accommodation_ids = json_decode($request->accommodation_ids);
            if (count($accommodation_ids) > 0) {
                $data->whereHas('accommodations', function($q) use ($accommodation_ids) {
                    $q->whereIn('item_id', $accommodation_ids);
                });
            }
        }
        if ($request->input('car_ids'))
        {
            $car_ids = json_decode($request->car_ids);
            if (count($car_ids) > 0) {
                $data->whereHas('cars', function($q) use ($car_ids) {
                    $q->whereIn('item_id', $car_ids);
                });
            }
        }
        if ($request->input('driver_ids'))
        {
            $driver_ids = json_decode($request->driver_ids);
            if (count($driver_ids) > 0) {
                $data->whereHas('drivers', function($q) use ($driver_ids) {
                    $q->whereIn('item_id', $driver_ids);
                });
            }
        }

        $data->orderBy('arrive_at', 'asc')->orderBy('arrive_time', 'asc');
        
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
            'paid',
            'arrive_time',
            'airline',
            'cost',
            'total',
            'status',
            'extra_services',
        ]);

        $data['user_id'] = $request->user()->id;

        $newRow = TableName::create($data);

        $data = TableName::find($newRow->id, [
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'paid',
            'arrive_time',
            'status',
            'airline',
            'extra_services',
        ])->toArray();

        if (isset($data['extra_services'])) {
            $data['extra_services'] = ExtraService::whereIn('id', json_decode($data['extra_services']))->withTrashed()->pluck('name')->toArray();
        }

        ActivitiesLog::create([
            'user_id' => $request->user()->id,
            'order_id' => $newRow->id,
            'item_id' => $newRow->id,
            'item_type' => TableName::class,
            'data' => $data,
            'type' => 'Add Reservation',
        ]);

        Notifications::sendOrderNotif($newRow->id, 'new');

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
            'paid',
            'arrive_time',
            'airline',
            'cost',
            'total',
            'total_special',
            'status',
            'extra_services',
        ]);

        $itemBeforeUpdate = TableName::where('id', $id)->select([
            'name',
            'phone',
            'arrive_at',
            'leave_at',
            'paid',
            'arrive_time',
            'airline',
            'total_special',
            'status',
            'extra_services'
        ])->first()->toArray();
        $newUpdates = Helper::arrayDiffValues($data, $itemBeforeUpdate);
        if (isset($newUpdates['extra_services'])) {
            $newUpdates['extra_services'] = ExtraService::whereIn('id', json_decode($newUpdates['extra_services']))->withTrashed()->pluck('name')->toArray();
        }
        
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
            Notifications::sendOrderNotif($item->id, 'new');
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
