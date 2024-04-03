<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'paid_at',
        'status',
        'currency',
        'cost',
        'price',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_at' => 'datetime'
    ];

    public function products()
    {
        return $this->hasMany(OrderProducts::class)->with('product');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function accommodations()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'accommodation');
    }

    public function cars()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'car');
    }

    public function calcTotals()
    {
        $products = $this->hasMany(OrderProducts::class)->get();
        $price = 0;
        $cost = 0;
        $total = 0;
        foreach ($products as $key => $value) {
            $price += $value->price * $value->days;
            $cost += $value->cost * $value->days;
            $total += $value->total;
        }
        $this->price = $price;
        $this->cost = $cost;
        $this->total = $total;
        $this->save();
    }

    public function getOrderRevenueAttribute()
    {
        $products = $this->hasMany(OrderProducts::class)->get();
        $cost = 0;
        $total = 0;
        foreach ($products as $key => $value) {
            $cost += $value->cost * $value->days;
            $total += $value->total;
        }
        return $total - $cost;
    }

}
