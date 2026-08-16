<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSupportController extends Controller
{
    public function index()
    {
        $user = Auth::guard('student')->user();

        $tickets = SupportTicket::where('user_id', $user->id)->latest()->get();

        return view('Users.User.support-index', compact('tickets'));
    }

    public function create()
    {
        return view('Users.User.support-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|in:technical,content,account,other',
            'message'  => 'required|string|max:3000',
        ]);

        $user = Auth::guard('student')->user();

        $ticket = SupportTicket::create([
            'user_id'  => $user->id,
            'subject'  => $request->subject,
            'category' => $request->category,
            'status'   => 'open',
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'student',
            'sender_id'         => $user->id,
            'message'           => $request->message,
        ]);

        $this->notifyOwnersOfNewTicket($ticket);

        return redirect()->route('student.support.show', $ticket->id)
            ->with('success', 'Ikibazo cyawe cyoherejwe. Turagusubiza vuba.');
    }

    /**
     * The first owner-facing notification anywhere in the app — every
     * existing Notification class was student-facing only. Notifies every
     * owner who actually holds support.view (not every owner in the
     * system), matching the RBAC boundary rather than broadcasting past it.
     *
     * Wrapped in try/catch: found via the Realtime infrastructure phase
     * that if the broadcast/WebSocket server is ever unreachable, the
     * unhandled connection exception from the 'broadcast' channel
     * propagated up and failed the ENTIRE ticket-creation request with a
     * 500 — meaning a temporary realtime outage would have broken a core
     * feature (submitting a support ticket) that has nothing to do with
     * realtime. The database notification and the ticket itself must
     * never depend on the broadcast transport being up.
     */
    private function notifyOwnersOfNewTicket(SupportTicket $ticket): void
    {
        $recipients = \App\Models\Owner::whereHas('roles.permissions', function ($q) {
            $q->where('slug', 'support.view');
        })->orWhereHas('roles', fn ($q) => $q->where('is_super', true))->get();

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\NewSupportTicketNotification($ticket));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Support ticket notification failed to fully dispatch (likely the broadcast/WebSocket server being unreachable) — the ticket itself was still created.', [
                'ticket_id' => $ticket->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $user = Auth::guard('student')->user();

        $ticket = SupportTicket::with('replies')->where('user_id', $user->id)->findOrFail($id);

        return view('Users.User.support-show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $user = Auth::guard('student')->user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        $request->validate(['message' => 'required|string|max:3000']);

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']); // replying reopens a closed ticket
        }

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'student',
            'sender_id'         => $user->id,
            'message'           => $request->message,
        ]);

        return back()->with('success', 'Ubutumwa bwoherejwe.');
    }
}
