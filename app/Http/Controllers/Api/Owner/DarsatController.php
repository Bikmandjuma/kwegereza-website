<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Owner\StoreDarsatRequest;
use App\Http\Requests\Api\Owner\UpdateDarsatRequest;
use App\Http\Resources\DarsatResource;
use App\Models\DarsatTable;
use App\Services\DarsatService;
use Illuminate\Http\Request;

class DarsatController extends Controller
{
    public function __construct(private DarsatService $darsat)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->darsat->paginate(
            perPage: (int) $request->integer('per_page', 10),
            search: $request->string('search')->value() ?: null,
            status: $request->string('status')->value() ?: null,
            teacherId: $request->integer('teacher_id') ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => DarsatResource::collection($paginated->items()),
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
            'data' => new DarsatResource($this->darsat->find($id)),
        ]);
    }

    public function store(StoreDarsatRequest $request)
    {
        $darsat = $this->darsat->create(
            $request->validated(),
            $request->file('audio'),
            $request->file('thumbnail'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Darsat yashyizweho.',
            'data' => new DarsatResource($darsat),
        ], 201);
    }

    public function update(UpdateDarsatRequest $request, int $id)
    {
        $darsat = DarsatTable::findOrFail($id);

        $this->darsat->update(
            $darsat,
            $request->validated(),
            $request->file('audio'),
            $request->file('thumbnail'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Darsat yahinduwe.',
            'data' => new DarsatResource($darsat->fresh('teacher')),
        ]);
    }

    public function destroy(int $id)
    {
        $this->darsat->delete(DarsatTable::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Darsat yasibwe.', 'data' => null]);
    }
}
