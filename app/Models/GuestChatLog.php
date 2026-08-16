<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestChatLog extends Model
{
    use HasFactory;

    protected $table = 'guest_chat_logs';

    protected $fillable = [
        'guest_id', 'session_id', 'ip', 'page',
        'question', 'response', 'matched_question_id', 'was_matched',
    ];

    protected $casts = [
        'was_matched' => 'boolean',
    ];

    public function matchedQuestion()
    {
        return $this->belongsTo(ChatQuestion::class, 'matched_question_id');
    }
}
