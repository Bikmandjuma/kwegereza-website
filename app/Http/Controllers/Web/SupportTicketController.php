<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:support.view')->only(['index', 'show']);
        $this->middleware('permission:support.manage')->only(['reply', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        $tickets = SupportTicket::with('user')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $counts = [
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'closed'      => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('Users.admin.support-index', compact('tickets', 'counts', 'status'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['replies', 'user'])->findOrFail($id);

        return view('Users.admin.support-show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate(['message' => 'required|string|max:3000']);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'owner',
            'sender_id'         => Auth::guard('owner')->id(),
            'message'           => $request->message,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress', 'assigned_to' => Auth::guard('owner')->id()]);
        }

        return back()->with('success', 'Igisubizo cyoherejwe.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);

        SupportTicket::findOrFail($id)->update(['status' => $request->status]);

        return back()->with('success', 'Status yahinduwe.');
    }
}
