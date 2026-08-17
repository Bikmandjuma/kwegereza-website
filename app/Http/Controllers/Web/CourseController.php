<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\DarsatTable;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courses)
    {
        $this->middleware('permission:courses.view')->only(['index', 'show']);
        $this->middleware('permission:courses.create')->only(['store', 'storeLesson']);
        $this->middleware('permission:courses.update')->only(['update', 'updateLesson', 'reorderLessons']);
        $this->middleware('permission:courses.delete')->only(['destroy', 'destroyLesson']);
    }

    public function index()
    {
        $courses = $this->courses->paginate(10);

        return view('Users.admin.courses', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $this->courses->create($data, $request->file('thumbnail'), auth('owner')->id());

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $this->courses->update($course, $data, $request->file('thumbnail'), auth('owner')->id());

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryahinduwe.');
    }

    public function destroy($id)
    {
        $this->courses->delete(Course::findOrFail($id));

        return redirect()->route('owner.courses')->with('success', 'Isomo ryagenda ryasibwe.');
    }

    /**
     * Lesson management within a course (the "learning path" builder)
     */
    public function show($id)
    {
        $course = $this->courses->findWithLessons($id);
        $darsatOptions = DarsatTable::published()->orderBy('title')->get();

        return view('Users.admin.course-builder', compact('course', 'darsatOptions'));
    }

    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'darsat_id'   => 'nullable|exists:darsat_tables,id',
            'is_required' => 'nullable|boolean',
        ]);
        $data['darsat_id'] = $data['darsat_id'] ?: null;
        $data['is_required'] = $request->boolean('is_required', true);

        $this->courses->createLesson($course, $data);

        return back()->with('success', 'Isomo ryongewe ku nzira y\'amasomo.');
    }

    public function updateLesson(Request $request, $lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'darsat_id'   => 'nullable|exists:darsat_tables,id',
            'is_required' => 'nullable|boolean',
        ]);
        $data['darsat_id'] = $data['darsat_id'] ?: null;
        $data['is_required'] = $request->boolean('is_required', true);

        $this->courses->updateLesson($lesson, $data);

        return back()->with('success', 'Isomo ryahinduwe.');
    }

    public function destroyLesson($lessonId)
    {
        $this->courses->deleteLesson(CourseLesson::findOrFail($lessonId));

        return back()->with('success', 'Isomo ryakuweho.');
    }

    /**
     * Drag-and-drop reorder — expects { order: [lessonId, lessonId, ...] }
     */
    public function reorderLessons(Request $request, $courseId)
    {
        $request->validate(['order' => 'required|array']);

        $this->courses->reorderLessons((int) $courseId, $request->order);

        return response()->json(['ok' => true]);
    }
}
