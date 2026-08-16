<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Shared Courses/Lessons business logic.
 *
 * Extracted so the existing Blade-driven Web\CourseController and the new
 * Api\Owner\CourseController (React) call the same code path instead of two
 * copies of the same rules drifting apart. Auth/ownership ("who is doing
 * this") stays in the controllers; this class only knows about Courses.
 */
class CourseService
{
    use HandlesFileUploads;

    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = null)
    {
        return Course::withCount('lessons')
            ->withCount('enrollments')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Course
    {
        return Course::withCount('lessons')->withCount('enrollments')->findOrFail($id);
    }

    public function findWithLessons(int $id): Course
    {
        return Course::with('lessons.darsat')->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $thumbnail, int $ownerId): Course
    {
        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $i = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        return Course::create([
            'title'        => $data['title'],
            'slug'         => $slug,
            'description'  => $data['description'] ?? null,
            'thumbnail'    => $thumbnail ? $this->storeUploadedFile($thumbnail, 'courses/thumbnails') : null,
            'status'       => $data['status'],
            'created_by'   => $ownerId,
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);
    }

    public function update(Course $course, array $data, ?UploadedFile $thumbnail, int $ownerId): Course
    {
        $thumbnailPath = $course->thumbnail;

        if ($thumbnail) {
            $this->deleteUploadedFile($course->thumbnail, 'courses/thumbnails');
            $thumbnailPath = $this->storeUploadedFile($thumbnail, 'courses/thumbnails');
        }

        $course->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'thumbnail'    => $thumbnailPath,
            'status'       => $data['status'],
            'updated_by'   => $ownerId,
            'published_at' => $data['status'] === 'published' ? ($course->published_at ?? now()) : null,
        ]);

        return $course;
    }

    public function delete(Course $course): void
    {
        $this->deleteUploadedFile($course->thumbnail, 'courses/thumbnails');
        $course->delete();
    }

    public function createLesson(Course $course, array $data): CourseLesson
    {
        $nextOrder = ($course->lessons()->max('order') ?? 0) + 1;

        return CourseLesson::create([
            'course_id'   => $course->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'content'     => $data['content'] ?? null,
            'darsat_id'   => $data['darsat_id'] ?? null,
            'order'       => $nextOrder,
            'is_required' => $data['is_required'] ?? true,
        ]);
    }

    public function updateLesson(CourseLesson $lesson, array $data): CourseLesson
    {
        $lesson->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'content'     => $data['content'] ?? null,
            'darsat_id'   => $data['darsat_id'] ?? null,
            'is_required' => $data['is_required'] ?? true,
        ]);

        return $lesson;
    }

    public function deleteLesson(CourseLesson $lesson): void
    {
        $lesson->delete();
    }

    public function reorderLessons(int $courseId, array $orderedLessonIds): void
    {
        foreach ($orderedLessonIds as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)
                ->where('course_id', $courseId)
                ->update(['order' => $index + 1]);
        }
    }
}
