<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    // use SoftDeletes;

    protected $table = "media";

    protected $appends = [];

    public static $default_image = 'images/default_image.png';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['type', 'url', 'thumbnail', 'name', 'hash'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * 
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    private static $videoExtensions = ['mp4','ogx','oga','ogv','ogg','webm','avi','3gp'];

    protected static function booted(): void
    {
        static::deleted(function (Media $file) {
            Storage::disk('public')->delete($file->attributes['url']);
        });
    }
    
    public function setUrlAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            $name = $value->store(null, 'public');  
            $this->attributes['url'] = $name;
            // if (in_array($this->fileExtension(), self::$videoExtensions))
            // {
            //     $this->setThumbnail();
            // }
        } else {
            $this->attributes['url'] = $value;
        }
    }

    public function getFileNameAttribute()
    {
        return $this->attributes['url'];
    }

    public function getUrlAttribute()
    {
        return $this->attributes['url'] ? Storage::disk('public')->url($this->attributes['url']) : asset(self::$default_image);
    }

    public function getThumbnailAttribute()
    {
        return $this->attributes['thumbnail'] ? Storage::disk('public')->url('thumbnails/'.$this->attributes['thumbnail']) : asset(self::$default_image);
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->attributes['url'], PATHINFO_EXTENSION);
    }

}
