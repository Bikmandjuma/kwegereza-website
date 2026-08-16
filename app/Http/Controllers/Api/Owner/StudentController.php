<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentDetailResource;
use App\Http\Resources\StudentResource;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private StudentService $students)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->students->paginate(
            perPage: (int) $request->integer('per_page', 20),
            search: $request->string('search')->value() ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => StudentResource::collection($paginated->items()),
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
            'data' => new StudentDetailResource($this->students->detail($id)),
        ]);
    }

    public function block(int $id)
    {
        $student = $this->students->block(User::findOrFail($id));

        return response()->json([
            'success' => true,
            'message' => $student->firstname.' yahagaritswe kubona urubuga.',
            'data' => new StudentResource($student),
        ]);
    }

    public function unblock(int $id)
    {
        $student = $this->students->unblock(User::findOrFail($id));

        return response()->json([
            'success' => true,
            'message' => $student->firstname.' yongeye kubona urubuga.',
            'data' => new StudentResource($student),
        ]);
    }

    public function online()
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => [
                'count' => $this->students->onlineCount(),
                'students' => $this->students->onlineStudents()->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => trim($s->firstname.' '.$s->lastname),
                    'last_active_at' => $s->last_active_at,
                ]),
            ],
        ]);
    }
}
