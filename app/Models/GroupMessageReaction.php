<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessageReaction extends Model
{
    protected $fillable = ['group_message_id', 'actor_type', 'actor_id', 'emoji'];

    public function message()
    {
        return $this->belongsTo(GroupMessage::class, 'group_message_id');
    }
}
