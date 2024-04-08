<?php

namespace App\Models;

use App\Http\Resources\OrderProductResource;
use App\Http\Resources\OrderResource;
use Illuminate\Database\Eloquent\Model;

class ActivitiesLog extends Model
{
    protected $table = 'activities_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'item_id',
        'item_type',
        'data',
        'type'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->morphTo('item');
    }

}
