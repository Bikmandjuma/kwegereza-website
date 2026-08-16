<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerNotificationResource;
use App\Services\OwnerNotificationService;
use Illuminate\Http\Request;

/**
 * Deliberately NOT permission-gated beyond auth:sanctum — every logged-in
 * owner reads and manages only their own notification inbox, same as the
 * existing student-side NotificationController. There's no "view other
 * owners' notifications" concern here, so a granular permission slug
 * doesn't apply.
 */
class NotificationController extends Controller
{
    public function __construct(private OwnerNotificationService $notifications)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->notifications->paginate($request->user(), (int) $request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => OwnerNotificationResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ['count' => $this->notifications->unreadCount($request->user())],
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $this->notifications->markRead($request->user(), $id);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function markAllRead(Request $request)
    {
        $this->notifications->markAllRead($request->user());

        return response()->json(['success' => true, 'message' => 'Byose byasomwe.', 'data' => null]);
    }
}
