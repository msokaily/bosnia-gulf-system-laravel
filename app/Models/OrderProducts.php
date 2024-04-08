<?php

namespace App\Models;

use App\Http\Resources\AccommodationResource;
use App\Http\Resources\CarResource;
use App\Http\Resources\DriverResource;
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

    public function getProductAttribute()
    {
        switch ($this->type) {
            case 'accommodation':
                return new AccommodationResource($this->belongsTo(Accommodation::class, 'item_id', 'id')->first());
            break;
            case 'driver':
                return new DriverResource($this->belongsTo(Driver::class, 'item_id', 'id')->first());
            break;
            default:
                return new CarResource($this->belongsTo(Car::class, 'item_id', 'id')->first());
            break;
        }
    }

    public function getExtraValueAttribute()
    {
        switch ($this->type) {
            case 'accommodation':
                // return new AccommodationResource($this->belongsTo(Accommodation::class, 'item_id', 'id')->first());
            break;
            case 'driver':
                return new CarResource($this->belongsTo(Car::class, 'extra', 'id')->first());
            break;
            default:
                // return new CarResource($this->belongsTo(Car::class, 'item_id', 'id')->first());
            break;
        }
        return null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function getDaysAttribute()
    {
        $start_at = Carbon::parse($this->start_at);
        $end_at = Carbon::parse($this->end_at);
        return $end_at->diffInDays($start_at);
    }

}
