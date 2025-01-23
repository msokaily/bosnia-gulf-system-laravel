<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Payments;
use App\Models\User;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function privacy()
    {
        return view('privacy');
    }

    public function print($id)
    {
        $order = Order::findOrFail($id);
        $data['order'] = $order;
        if ($order->payments && count($order->payments) > 0) {
            $data['down_payment'] = $order->payments()->where('type', 'payment')->orderBy('created_at', 'ASC')->sum('amount') ?? 0;
            $data['deposit'] = $order->payments()->where('type', 'deposit')->sum('amount') ?? 0;
        }
        dd($data['down_payment'], $order->total_special);
        return view('print', $data);
    }

    public function constants()
    {
        return $this->resJson([
            'roles' => Helper::$roles,
            'partners_types' => Helper::$parnersTypes,
            'product_types' => Helper::$productTypes,
            'repair_types' => Helper::$repairTypes,
            'order_status' => Helper::$orderStatus,
        ]);
    }

    public function stats(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $currencies = Helper::$currencies;
        
        // Orders
        $dateFieldName = 'arrive_at';
        $orders = Order::query()->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        $data['orders'] = [
            'all' => [
                'count' => (clone $orders)->count(),
                'total' => (clone $orders)->sum('total'),
                'revenue' => (clone $orders)->sum('price') - (clone $orders)->sum('cost'),
            ],
            'new' => [
                'status' => 0,
                'count' => (clone $orders)->where('status', '0')->count(),
                'total' => (clone $orders)->where('status', '0')->sum('total'),
                'revenue' => (clone $orders)->where('status', '0')->get()->sum('orderRevenue'),
            ],
            'confirmed' => [
                'status' => 1,
                'count' => (clone $orders)->where('status', '1')->count(),
                'total' => (clone $orders)->where('status', '1')->sum('total'),
                'revenue' => (clone $orders)->where('status', '1')->sum('price') - (clone $orders)->where('status', '1')->sum('cost'),
            ],
            'completed' => [
                'status' => 2,
                'count' => (clone $orders)->where('status', '2')->count(),
                'total' => (clone $orders)->where('status', '2')->sum('total'),
                'revenue' => (clone $orders)->where('status', '2')->sum('price') - (clone $orders)->where('status', '2')->sum('cost'),
            ],
            'canceled' => [
                'status' => 3,
                'count' => (clone $orders)->where('status', '3')->count(),
                'total' => (clone $orders)->where('status', '3')->sum('total'),
                'revenue' => (clone $orders)->where('status', '3')->sum('price') - (clone $orders)->where('status', '3')->sum('cost'),
            ],
            'refunded' => [
                'status' => 4,
                'count' => (clone $orders)->where('status', '4')->count(),
                'total' => (clone $orders)->where('status', '4')->sum('total'),
                'revenue' => (clone $orders)->where('status', '4')->sum('price') - (clone $orders)->where('status', '4')->sum('cost'),
            ],
        ];
        foreach ($data['orders'] as $key => $value) {
            if (isset($value['status'])) {
                foreach ($currencies as $curr) {
                    $data['orders'][$key]['paid'][$curr] = Payments::query()
                    ->where('currency', $curr)
                    ->whereHas('order', function($q) use ($value, $year, $month, $dateFieldName) {
                        $q->where('status', $value['status']);
                        $q->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
                    })->sum('amount');
                }
            } else {
                foreach ($currencies as $curr) {
                    $data['orders'][$key]['paid'][$curr] = Payments::query()->where('currency', $curr)->whereHas('order', function($q) use ($year, $month, $dateFieldName) {
                        $q->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
                    })->sum('amount');
                }
            }
        }

        // Cars Orders
        $items_orders_query = OrderProducts::query()->where('type', 'car')->whereHas('order', function($q) use ($year, $month, $dateFieldName) {
            $q->whereIn('status', ['1', '2']);
            $q->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        });
        $items_orders = [];
        foreach ($items_orders_query->get() as $value) {
            if (!isset($items_orders[$value->item_id])) {
                $items_orders[$value->item_id] = [
                    'total' => 0,
                    'cost' => 0,
                    'price' => 0,
                    'count' => 0,
                ];
            }
            $items_orders[$value->item_id]['product'] = $value->product;
            $items_orders[$value->item_id]['total'] += $value->total;
            $items_orders[$value->item_id]['price'] += $value->price * $value->days;
            $items_orders[$value->item_id]['cost'] += $value->cost * $value->days;
            $items_orders[$value->item_id]['count'] += 1;
        }
        $data['cars_orders'] = array_values($items_orders);

        // Accommodations Orders
        $items_orders_query = OrderProducts::query()->where('type', 'accommodation')->whereHas('order', function($q) use ($year, $month, $dateFieldName) {
            $q->whereIn('status', ['1', '2']);
            $q->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        });
        $items_orders = [];
        foreach ($items_orders_query->get() as $value) {
            if (!isset($items_orders[$value->item_id])) {
                $items_orders[$value->item_id] = [
                    'total' => 0,
                    'cost' => 0,
                    'price' => 0,
                    'count' => 0,
                ];
            }
            $items_orders[$value->item_id]['product'] = $value->product;
            $items_orders[$value->item_id]['total'] += $value->total;
            $items_orders[$value->item_id]['price'] += $value->price * $value->days;
            $items_orders[$value->item_id]['cost'] += $value->cost * $value->days;
            $items_orders[$value->item_id]['count'] += 1;
        }
        $data['accommodations_orders'] = array_values($items_orders);
        
        // Drivers Orders
        $items_orders_query = OrderProducts::query()->where('type', 'driver')->whereHas('order', function($q) use ($year, $month, $dateFieldName) {
            $q->whereIn('status', ['1', '2']);
            $q->whereYear($dateFieldName, $year)->whereMonth($dateFieldName, $month);
        });
        $items_orders = [];
        foreach ($items_orders_query->get() as $value) {
            if (!isset($items_orders[$value->item_id])) {
                $items_orders[$value->item_id] = [
                    'total' => 0,
                    'cost' => 0,
                    'price' => 0,
                    'count' => 0,
                ];
            }
            $items_orders[$value->item_id]['product'] = $value->product;
            $items_orders[$value->item_id]['total'] += $value->total;
            $items_orders[$value->item_id]['price'] += $value->price * $value->days;
            $items_orders[$value->item_id]['cost'] += $value->cost * $value->days;
            $items_orders[$value->item_id]['count'] += 1;
        }
        $data['drivers_orders'] = array_values($items_orders);
        
        // Users
        $data['users'] = [
            'all' => User::count(),
            'active' => User::where('status', 1)->count(),
            'inactive' => User::whereNot('status', 1)->count()
        ];
        
        return $this->resJson($data);
    }
}
