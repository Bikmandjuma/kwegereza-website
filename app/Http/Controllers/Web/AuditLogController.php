<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:audit_logs.view');
    }

    public function index(Request $request)
    {
        $entityType = $request->input('entity_type');
        $action = $request->input('action');

        $logs = AuditLog::with('owner')
            ->when($entityType, fn($q) => $q->where('entity_type', 'like', "%{$entityType}%"))
            ->when($action, fn($q) => $q->where('action', $action))
            // See the API controller's identical fix for why id DESC is
            // needed alongside latest() — otherwise same-timestamp rows
            // (a seeder run, bulk import, or just a fast burst of
            // requests) sort ambiguously and a real entry can silently
            // fall behind older ones on the same page.
            ->latest()
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $entityTypes = AuditLog::select('entity_type')->distinct()->pluck('entity_type');

        return view('Users.admin.audit-logs', compact('logs', 'entityTypes', 'entityType', 'action'));
    }
}
