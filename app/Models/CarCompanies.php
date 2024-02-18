<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CarCompanies extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image'
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

}
