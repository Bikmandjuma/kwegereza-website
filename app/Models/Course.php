<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, \App\Traits\Favoritable;

    protected $fillable = [
        'title', 'slug', 'description', 'thumbnail', 'status',
        'created_by', 'updated_by', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('order');
    }

    public function requiredLessons()
    {
        return $this->lessons()->where('is_required', true);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function thumbnailUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->thumbnail, 'courses/thumbnails');
    }

    public function isEnrolledBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    /**
     * Percentage of this course's required lessons a given student has
     * completed. Used for both the enrollment's own completed_at trigger
     * and for progress bars in the UI.
     */
    public function completionPercentFor(int $userId): int
    {
        $required = $this->requiredLessons()->pluck('id');

        if ($required->isEmpty()) {
            return 0;
        }

        $completed = CourseLessonCompletion::where('user_id', $userId)
            ->whereIn('course_lesson_id', $required)
            ->count();

        return (int) round(($completed / $required->count()) * 100);
    }
}
