<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRepairs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'repair_id',
        'type',
        'price',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

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

    public function repair() {
        return $this->belongsTo(Repair::class, 'order_id', 'id');
    }

}
