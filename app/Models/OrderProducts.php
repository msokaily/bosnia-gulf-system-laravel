<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'item_id',
        'order_id',
        'start_at',
        'end_at',
        'note',
        'extra',
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
    ];

    public function product()
    {
        if ($this->type == 'accommodation')
        {
            return $this->belongsTo(Accommodation::class, 'item_id', 'id');
        }
        elseif ($this->type == 'driver')
        {
            return $this->belongsTo(Driver::class, 'item_id', 'id');
        }
        else
        {
            return $this->belongsTo(Car::class, 'item_id', 'id');
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function getDaysAttribute()
    {
        $start_at = Carbon::parse($this->start_at);
        $end_at = Carbon::parse($this->end_at);
        return $end_at->diffInDays($start_at) + 1;
    }

}
