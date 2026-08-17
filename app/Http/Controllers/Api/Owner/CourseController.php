<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Owner\StoreCourseRequest;
use App\Http\Requests\Api\Owner\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courses)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->courses->paginate(
            perPage: (int) $request->integer('per_page', 10),
            search: $request->string('search')->value() ?: null,
            status: $request->string('status')->value() ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => CourseResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => new CourseResource($this->courses->findWithLessons($id)),
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = $this->courses->create(
            $request->validated(),
            $request->file('thumbnail'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Isomo ryagenda ryashyizweho.',
            'data' => new CourseResource($course),
        ], 201);
    }

    public function update(UpdateCourseRequest $request, int $id)
    {
        $course = Course::findOrFail($id);

        $this->courses->update(
            $course,
            $request->validated(),
            $request->file('thumbnail'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Isomo ryagenda ryahinduwe.',
            'data' => new CourseResource($course->fresh()),
        ]);
    }

    public function destroy(int $id)
    {
        $this->courses->delete(Course::findOrFail($id));

        return response()->json([
            'success' => true,
            'message' => 'Isomo ryagenda ryasibwe.',
            'data' => null,
        ]);
    }
}
