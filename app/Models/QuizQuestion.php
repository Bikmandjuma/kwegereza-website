<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'question', 'type', 'short_answer', 'points', 'order', 'time_limit_seconds'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class)->orderBy('order');
    }

    public function correctAnswer()
    {
        return $this->answers()->where('is_correct', true)->first();
    }
}
