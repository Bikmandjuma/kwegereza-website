<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'reported_by',
        'reason',
        'status',
    ];


    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }


    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}