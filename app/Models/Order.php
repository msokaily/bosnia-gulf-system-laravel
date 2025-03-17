<?php

namespace App\Models;

use App\Http\Resources\ActivityLogsResource;
use App\Http\Resources\ExtraServiceResource;
use Helper;
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
        'user_id',
        'phone',
        'arrive_at',
        'leave_at',
        'paid',
        'arrive_time',
        'airline',
        'status',
        'currency',
        'cost',
        'price',
        'total',
        'total_special',
        'extra_services',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * The attributes that should be appended.
     *
     * @var array<string, string>
     */
    protected $appends = [
        'status_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProducts::class);
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function getPaidEurAttribute()
    {
        return $this->hasMany(Payments::class)->where('type', 'payment')->where('currency', 'EUR')->sum('amount');
    }

    public function accommodations()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'accommodation');
    }

    public function cars()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'car');
    }

    public function drivers()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'driver');
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

    public function setExtraServicesAttribute($value)
    {
        $extraServiceIds = json_decode($value ?? '[]', true);
        if (!is_array($extraServiceIds)) {
            $extraServiceIds = [];
        }
        $this->attributes['extra_services'] = json_encode($extraServiceIds);
    }

    public function getExtraServicesListAttribute()
    {
        $extraServiceIds = json_decode($this->attributes['extra_services'] ?? '[]', true);
        if (!is_array($extraServiceIds)) {
            $extraServiceIds = [];
        }

        return ExtraServiceResource::collection(
            ExtraService::whereIn('id', $extraServiceIds)->withTrashed()->get()
        );
    }

    public function getStatusNameAttribute()
    {
        return Helper::$orderStatus[$this->status]['en'] ?? 'New';
    }

    public function getLogsAttribute()
    {
        return ActivityLogsResource::collection($this->hasMany(ActivitiesLog::class)->orderBy('created_at', 'DESC')->get());
    }

    public function getProductAttribute()
    {
        return null;
    }

    public function getDownPaymentAttribute()
    {
        return $this->payments()->where('type', 'down_payment')->sum('amount') ?? 0;
    }

    public function getDepositAttribute()
    {
        return $this->payments()->where('type', 'deposit')->sum('amount') ?? 0;
    }
}
