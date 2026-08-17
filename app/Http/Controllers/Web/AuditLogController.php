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
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $entityTypes = AuditLog::select('entity_type')->distinct()->pluck('entity_type');

        return view('Users.admin.audit-logs', compact('logs', 'entityTypes', 'entityType', 'action'));
    }
}
