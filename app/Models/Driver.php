<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'cost',
        'price',
        'multiple',
        'status',
    ];

    public function active_reservations()
    {
        return $this->hasMany(OrderProducts::class, 'item_id', 'id')->where('type', 'driver')->whereHas('order', function($q) {
            $q->whereIn('status', ['1', 1]);
        });
    }
    
}
