<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Owner\StoreAnnouncementRequest;
use App\Http\Requests\Api\Owner\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Amatangazo;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct(private AnnouncementService $announcements)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->announcements->paginate(
            perPage: (int) $request->integer('per_page', 10),
            search: $request->string('search')->value() ?: null,
            status: $request->string('status')->value() ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => AnnouncementResource::collection($paginated->items()),
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
            'data' => new AnnouncementResource($this->announcements->find($id)),
        ]);
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $announcement = $this->announcements->create(
            $request->validated(),
            $request->file('image'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Itangazo ryashyizweho.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function update(UpdateAnnouncementRequest $request, int $id)
    {
        $announcement = Amatangazo::findOrFail($id);

        $this->announcements->update(
            $announcement,
            $request->validated(),
            $request->file('image'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Itangazo ryahinduwe.',
            'data' => new AnnouncementResource($announcement->fresh()),
        ]);
    }

    public function destroy(int $id)
    {
        $this->announcements->delete(Amatangazo::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Itangazo ryasibwe.', 'data' => null]);
    }

    public function togglePublish(int $id)
    {
        $announcement = $this->announcements->togglePublish(Amatangazo::findOrFail($id));

        return response()->json([
            'success' => true,
            'message' => $announcement->is_published ? 'Itangazo ryerekanwa.' : 'Itangazo ryahishwe.',
            'data' => new AnnouncementResource($announcement),
        ]);
    }
}
