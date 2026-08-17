<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatPresence extends Model
{
    use HasFactory;

    protected $table = 'chat_presence';

    protected $fillable = [
        'guest_id',
        'last_seen',
        'typing',
        'admin_typing'
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'typing' => 'boolean',
        'admin_typing' => 'boolean',
    ];
}