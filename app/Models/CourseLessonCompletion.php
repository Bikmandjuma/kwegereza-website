<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_lesson_id', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
