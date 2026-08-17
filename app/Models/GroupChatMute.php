<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatMute extends Model
{
    protected $fillable = ['group', 'actor_type', 'actor_id', 'muted_by', 'muted_until'];

    protected $casts = [
        'muted_until' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->muted_until === null || $this->muted_until->isFuture();
    }
}
