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

        return redirect()->route('student.support.show', $ticket->id)
            ->with('success', 'Ikibazo cyawe cyoherejwe. Turagusubiza vuba.');
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
