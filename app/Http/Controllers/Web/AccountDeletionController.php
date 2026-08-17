<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountDeletionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:account_deletion.manage');
    }

    public function index()
    {
        $requests = AccountDeletionRequest::with('user')->latest()->paginate(15);

        return view('Users.admin.account-deletions', compact('requests'));
    }

    public function approve($id)
    {
        $request = AccountDeletionRequest::findOrFail($id);

        $request->update([
            'status'       => 'approved',
            'processed_by' => Auth::guard('owner')->id(),
            'processed_at' => now(),
        ]);

        // Deactivate rather than hard-delete — this account has certificates,
        // comments, and other historical records referencing it across many
        // tables built this session. Deactivation blocks login and access
        // immediately (see StudentAuthMiddleware) without silently orphaning
        // or cascading through records that other people/processes may
        // still legitimately need (e.g. a certificate someone else verifies).
        $request->user->update(['deactivated_at' => now()]);

        return back()->with('success', 'Konti yahagaritswe.');
    }

    public function reject($id)
    {
        $request = AccountDeletionRequest::findOrFail($id);

        $request->update([
            'status'       => 'rejected',
            'processed_by' => Auth::guard('owner')->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Icyifuzo cyanzwe.');
    }
}
