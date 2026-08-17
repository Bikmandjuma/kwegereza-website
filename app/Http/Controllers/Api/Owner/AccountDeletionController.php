<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use Illuminate\Http\Request;

class AccountDeletionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->value() ?: null;

        $paginated = AccountDeletionRequest::with('user')
            ->when($search, fn ($q) => $q->whereHas('user', fn ($q2) => $q2->where('firstname', 'like', "%{$search}%")
                ->orWhere('lastname', 'like', "%{$search}%")))
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return response()->json([
            'success' => true, 'message' => null,
            'data' => collect($paginated->items())->map(fn ($r) => [
                'id' => $r->id, 'status' => $r->status, 'reason' => $r->reason ?? null,
                'user' => $r->user ? ['id' => $r->user->id, 'name' => trim($r->user->firstname.' '.$r->user->lastname)] : null,
                'created_at' => $r->created_at, 'processed_at' => $r->processed_at,
            ]),
            'meta' => ['current_page' => $paginated->currentPage(), 'last_page' => $paginated->lastPage(), 'total' => $paginated->total()],
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $deletionRequest = AccountDeletionRequest::findOrFail($id);

        $deletionRequest->update(['status' => 'approved', 'processed_by' => $request->user()->id, 'processed_at' => now()]);
        $deletionRequest->user->update(['deactivated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Konti yahagaritswe.', 'data' => null]);
    }

    public function reject(Request $request, int $id)
    {
        $deletionRequest = AccountDeletionRequest::findOrFail($id);

        $deletionRequest->update(['status' => 'rejected', 'processed_by' => $request->user()->id, 'processed_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Icyifuzo cyanzwe.', 'data' => null]);
    }
}
