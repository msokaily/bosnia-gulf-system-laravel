<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatgptMessage extends Model
{

    protected $table = "chatgpt_messeges";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_whatsapp_id',
        'user_phone',
        'data',
        'type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'object'
    ];

}
