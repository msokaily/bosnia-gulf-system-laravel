<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{

    protected $table = "whatsapp_messeges";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_whatsapp_id',
        'user_phone',
        'sender',
        'message_id',
        'data',
        'type',
        'read_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'object',
        'read_at' => 'date:Y-m-d H:m'
    ];

}
