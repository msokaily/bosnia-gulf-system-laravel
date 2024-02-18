<?php

namespace App\Models;

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
        'cost',
        'price',
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
        else
        {
            return $this->belongsTo(Car::class, 'item_id', 'id');
        }
    }

}
