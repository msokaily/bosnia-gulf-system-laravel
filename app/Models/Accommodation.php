<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Accommodation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'partner_id',
        'type',
        'cost',
        'price',
        'address',
        'location',
        'image',
        'multiple',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    /**
     * The attributes that should be append.
     *
     * @var array<string>
     */
    protected $appends = [
    ];

    public function setImageAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            $this->attributes['image'] = $value->store(null, 'public');
        } else {
            $this->attributes['image'] = $value;
        }
    }

    public function getImageAttribute()
    {
        return $this->attributes['image'] ? 
            Storage::disk('public')->url($this->attributes['image']) : null;
    }

    public function active_reservations()
    {
        return $this->hasMany(OrderProducts::class, 'item_id', 'id')->where('type', 'accommodation')->whereHas('order', function($q) {
            $q->whereIn('status', ['1', 1]);
        });
    }

}
