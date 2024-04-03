<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarRepairs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'car_id',
        'repair_id',
        'price',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function car() {
        return $this->belongsTo(Car::class);
    }

    public function repairs() {
        return $this->hasMany(Repair::class);
    }

}
