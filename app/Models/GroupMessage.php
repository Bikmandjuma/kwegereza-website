<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMessage extends Model
{
    use SoftDeletes;

    public const GROUP_MALE_STUDENTS = 'male_students';
    public const GROUP_FEMALE_STUDENTS = 'female_students';
    public const GROUP_LEADERS = 'leaders';

    public const GROUPS = [self::GROUP_MALE_STUDENTS, self::GROUP_FEMALE_STUDENTS, self::GROUP_LEADERS];

    protected $fillable = [
        'group', 'sender_type', 'sender_id', 'sender_name', 'message', 'parent_id',
        'pinned_at', 'pinned_by_type', 'pinned_by_id',
    ];

    protected $casts = [
        'pinned_at' => 'datetime',
    ];

    public function scopeForGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePinned($query)
    {
        return $query->whereNotNull('pinned_at');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function reactions()
    {
        return $this->hasMany(GroupMessageReaction::class);
    }

    public function reports()
    {
        return $this->hasMany(GroupMessageReport::class);
    }

    public function isPinned(): bool
    {
        return $this->pinned_at !== null;
    }
}
