<?php

namespace App\Http\Controllers;

use Helper;
use Illuminate\Http\Request;

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

    public function constants()
    {
        return $this->resJson([
            'roles' => Helper::$roles,
            'partners_types' => Helper::$parnersTypes,
            'product_types' => Helper::$productTypes,
            'order_status' => Helper::$orderStatus,
        ]);
    }
}
