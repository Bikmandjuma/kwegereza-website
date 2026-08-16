<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessageReport extends Model
{
    protected $fillable = [
        'group_message_id', 'reporter_type', 'reporter_id', 'reason',
        'status', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(GroupMessage::class, 'group_message_id');
    }
}
