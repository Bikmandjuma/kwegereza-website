<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificates)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->certificates->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
        );

        return response()->json([
            'success' => true, 'message' => null,
            'data' => CertificateResource::collection($paginated->items()),
            'meta' => ['current_page' => $paginated->currentPage(), 'last_page' => $paginated->lastPage(), 'total' => $paginated->total()],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $certificate = $this->certificates->issue(
            User::findOrFail($data['user_id']), $data['title'], $data['course_id'] ?? null, $request->user()->id
        );

        return response()->json([
            'success' => true, 'message' => 'Icyemezo cyatanzwe.',
            'data' => new CertificateResource($certificate->load(['user', 'course'])),
        ], 201);
    }
}
