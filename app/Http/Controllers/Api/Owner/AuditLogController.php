<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $entityType = $request->string('entity_type')->value() ?: null;
        $action = $request->string('action')->value() ?: null;

        $logs = AuditLog::with('owner')
            ->when($entityType, fn ($q) => $q->where('entity_type', 'like', "%{$entityType}%"))
            ->when($action, fn ($q) => $q->where('action', $action))
            // A plain latest() (orderByDesc('created_at') alone) has no
            // tie-breaker when many rows share the same timestamp — which
            // happens routinely (a seeder run, a bulk import, or just
            // fast successive requests all landing in the same second).
            // Without id DESC as a secondary sort, "most recent first" is
            // genuinely ambiguous among tied rows, and a real entry can
            // silently fall off the first page behind older ones that
            // happen to sort first for that tie.
            ->latest()
            ->orderByDesc('id')
            ->paginate((int) $request->integer('per_page', 25));

        return response()->json([
            'success' => true, 'message' => null,
            'data' => AuditLogResource::collection($logs->items()),
            'meta' => ['current_page' => $logs->currentPage(), 'last_page' => $logs->lastPage(), 'total' => $logs->total()],
            'entity_types' => AuditLog::select('entity_type')->distinct()->pluck('entity_type'),
        ]);
    }
}
