<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'quizzable_id', 'quizzable_type',
        'passing_percentage', 'status', 'starts_at', 'duration_minutes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * A quiz with no starts_at is available any time. One with a future
     * starts_at is visible (students should see it's coming) but not yet
     * takeable.
     */
    public function hasStarted(): bool
    {
        return !$this->starts_at || $this->starts_at->isPast();
    }

    /**
     * Whether a student can actually open and submit this quiz right now.
     * Checked both when rendering the "Start" button and, critically,
     * server-side on every take()/submit() call — a link disabled in the
     * UI is not real access control.
     */
    public function isTakeableNow(): bool
    {
        return $this->status === 'published' && $this->hasStarted();
    }

    /**
     * Quizzes relevant to a given student: standalone (available to
     * everyone), attached to a course they're enrolled in, or attached to
     * a Darsat lesson they have progress on. Centralized here so the
     * dashboard widget, the quiz history "undone" list, and the admin
     * notification dispatch all agree on what "relevant" means.
     */
    public static function relevantToStudent(int $userId)
    {
        $enrolledCourseIds = \App\Models\CourseEnrollment::where('user_id', $userId)->pluck('course_id');
        $courseLessonIds = \App\Models\CourseLesson::whereIn('course_id', $enrolledCourseIds)->pluck('id');
        $darsatIds = \App\Models\DarsatProgress::where('user_id', $userId)->pluck('darsat_id');

        return static::published()->where(function ($q) use ($courseLessonIds, $darsatIds) {
            $q->whereNull('quizzable_type')
              ->orWhere(function ($q2) use ($courseLessonIds) {
                  $q2->where('quizzable_type', \App\Models\CourseLesson::class)->whereIn('quizzable_id', $courseLessonIds);
              })
              ->orWhere(function ($q3) use ($darsatIds) {
                  $q3->where('quizzable_type', \App\Models\DarsatTable::class)->whereIn('quizzable_id', $darsatIds);
              });
        });
    }

    public function quizzable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }

    public function bestAttemptFor(int $userId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->orderByDesc('percentage')
            ->first();
    }

    public function hasPassedBy(int $userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }
}
