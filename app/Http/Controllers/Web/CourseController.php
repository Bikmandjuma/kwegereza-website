<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\DarsatTable;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class CourseController extends Controller
{
    use HandlesFileUploads;

    public function __construct()
    {
        $this->middleware('permission:courses.view')->only(['index', 'show']);
        $this->middleware('permission:courses.create')->only(['store', 'storeLesson']);
        $this->middleware('permission:courses.update')->only(['update', 'updateLesson', 'reorderLessons']);
        $this->middleware('permission:courses.delete')->only(['destroy', 'destroyLesson']);
    }

    public function index()
    {
        $courses = Course::withCount('lessons')->withCount('enrollments')->latest()->paginate(10);

        return view('Users.admin.courses', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $i = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $thumbnail = $this->storeUploadedFile($request->file('thumbnail'), 'courses/thumbnails');

        Course::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'description'  => $request->description,
            'thumbnail'    => $thumbnail,
            'status'       => $request->status,
            'created_by'   => auth('owner')->id(),
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $thumbnail = $course->thumbnail;

        if ($request->hasFile('thumbnail')) {
            $this->deleteUploadedFile($course->thumbnail, 'courses/thumbnails');
            $thumbnail = $this->storeUploadedFile($request->file('thumbnail'), 'courses/thumbnails');
        }

        $course->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'thumbnail'    => $thumbnail,
            'status'       => $request->status,
            'updated_by'   => auth('owner')->id(),
            'published_at' => $request->status === 'published' ? ($course->published_at ?? now()) : null,
        ]);

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryahinduwe.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $this->deleteUploadedFile($course->thumbnail, 'courses/thumbnails');
        $course->delete();

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryasibwe.');
    }

    /**
     * Lesson management within a course (the "learning path" builder)
     */
    public function show($id)
    {
        $course = Course::with('lessons.darsat')->findOrFail($id);
        $darsatOptions = DarsatTable::published()->orderBy('title')->get();

        return view('Users.admin.course-builder', compact('course', 'darsatOptions'));
    }

    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'darsat_id'   => 'nullable|exists:darsat_tables,id',
            'is_required' => 'nullable|boolean',
        ]);

        $nextOrder = ($course->lessons()->max('order') ?? 0) + 1;

        CourseLesson::create([
            'course_id'   => $course->id,
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => $request->content,
            'darsat_id'   => $request->darsat_id ?: null,
            'order'       => $nextOrder,
            'is_required' => $request->boolean('is_required', true),
        ]);

        return back()->with('success', 'Isomo ryongewe ku nzira y\'amasomo.');
    }

    public function updateLesson(Request $request, $lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'darsat_id'   => 'nullable|exists:darsat_tables,id',
            'is_required' => 'nullable|boolean',
        ]);

        $lesson->update([
            'title'       => $request->title,
            'description' => $request->description,
            'content'     => $request->content,
            'darsat_id'   => $request->darsat_id ?: null,
            'is_required' => $request->boolean('is_required', true),
        ]);

        return back()->with('success', 'Isomo ryahinduwe.');
    }

    public function destroyLesson($lessonId)
    {
        CourseLesson::findOrFail($lessonId)->delete();

        return back()->with('success', 'Isomo ryakuweho.');
    }

    /**
     * Drag-and-drop reorder — expects { order: [lessonId, lessonId, ...] }
     */
    public function reorderLessons(Request $request, $courseId)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)
                ->where('course_id', $courseId)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }
}
