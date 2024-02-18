<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\CarCompanies;

class Car extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'model',
        'company',
        'register_no',
        'register_start',
        'register_end',
        'owner',
        'partner_id',
        'image',
        'cost',
        'price',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'register_start' => 'date',
        'register_end' => 'date',
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

    public function company_ob()
    {
        return $this->hasOne(CarCompanies::class, 'id','company');
    }

}
