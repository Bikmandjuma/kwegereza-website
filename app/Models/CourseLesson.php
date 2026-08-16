<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'description', 'content',
        'darsat_id', 'order', 'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function darsat()
    {
        return $this->belongsTo(DarsatTable::class, 'darsat_id');
    }

    public function isCompletedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return CourseLessonCompletion::where('user_id', $userId)
            ->where('course_lesson_id', $this->id)
            ->exists();
    }

    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizzable');
    }
}
