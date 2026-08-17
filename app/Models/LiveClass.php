<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = ['title', 'host_id', 'status', 'scheduled_at', 'started_at', 'ended_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(Owner::class, 'host_id');
    }

    public function participants()
    {
        return $this->hasMany(LiveClassParticipant::class);
    }

    public function activeParticipants()
    {
        return $this->participants()->whereNull('left_at');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }
}
