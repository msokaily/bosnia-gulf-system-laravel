<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'paid_at',
        'payment_type',
        'cost',
        'total',
        'currency',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    public function products()
    {
        return $this->hasMany(OrderProducts::class);
    }

    public function accommodations()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'accommodation');
    }

    public function cars()
    {
        return $this->hasMany(OrderProducts::class)->where('type', 'car');
    }

}
