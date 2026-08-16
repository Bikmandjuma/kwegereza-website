<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'commentable_id', 'commentable_type', 'parent_id', 'content', 'status'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved')->oldest();
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function reports()
    {
        return $this->hasMany(CommentReport::class);
    }

    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'approved');
    }

    public function displayName(): string
    {
        if (!$this->user) {
            return 'Umunyeshuri';
        }

        if (!$this->user->profile_visible) {
            return 'Umunyeshuri (Anonymous)';
        }

        return trim($this->user->firstname . ' ' . $this->user->lastname) ?: 'Umunyeshuri';
    }
}
