<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ChatService;

class ChatController extends Controller
{
    public function __construct(private ChatService $chat)
    {
        // Guest-facing methods (sendMessage, messages, presence) are
        // intentionally left ungated here — they run on public routes with
        // no 'ownerAuth' middleware at all, so there is no owner to check a
        // permission against. Only the leader/admin side of Twandikire is
        // gated, per the spec: "Only users with chat.reply permission can
        // respond."
        $this->middleware('permission:chat.view')->only(['ownerchatroom', 'conversations', 'adminMessages', 'typingStatus']);
        $this->middleware('permission:chat.reply')->only(['adminSend', 'markAsRead', 'reportAdminTyping']);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'sender_type' => 'required|string',
            'sender_name' => 'required|string',
            'guest_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $this->chat->sendAsGuest($request->guest_id, $request->message, $request->sender_name);

        return response()->json(['success' => true]);
    }

    public function messages($guest_id)
    {
        return response()->json($this->chat->messagesFor($guest_id));
    }

    public function ownerchatroom()
    {
        return view('Users.admin.chatRoom');
    }

    public function conversations()
    {
        return response()->json($this->chat->conversations());
    }

    public function adminMessages($guest_id)
    {
        return response()->json($this->chat->messagesFor($guest_id));
    }

    public function adminSend(Request $request)
    {
        $this->chat->sendAsAdmin($request->guest_id, $request->message, Auth::guard('owner')->user()->firstname);

        return response()->json(['success' => true]);
    }

    public function presence(Request $request)
    {
        $this->chat->touchPresence($request->guest_id);

        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request)
    {
        $this->chat->markRead($request->guest_id);

        return response()->json(['success' => true]);
    }

    /**
     * Was referenced by ->middleware(...)->only(['typingStatus']) and by a
     * live route (GET /owner/chat/typing/{guest_id}) since this
     * controller's very first version, but the method itself never
     * existed — a real, confirmed bug found while building the Chat phase:
     * hitting that route has always thrown a fatal 500. Fixed by actually
     * implementing it. Reads whether the GUEST is currently typing (the
     * admin chatroom's own JS confirms this is what it expects).
     */
    public function typingStatus($guest_id)
    {
        return response()->json(['typing' => $this->chat->guestTypingStatus($guest_id)]);
    }

    /**
     * Guest reports their own typing state. Matches the real guest widget
     * exactly (twandikire.blade.php posts to /chat/typing with
     * {guest_id, typing} in the body — not a {guest_id} route segment,
     * which is what I assumed on the first pass before checking the
     * actual frontend code).
     */
    public function reportGuestTyping(Request $request)
    {
        $this->chat->setGuestTyping($request->guest_id, $request->boolean('typing'));

        return response()->json(['success' => true]);
    }

    /**
     * The admin's own typing state, reported from the owner chatroom.
     * Symmetric gap to the one above: the guest widget has always polled
     * GET /chat/admin-typing/{guestId} to show "admin is typing", but
     * nothing anywhere ever wrote this value and the route didn't exist —
     * confirmed by reading chatRoom.blade.php, which reports the guest's
     * typing status to itself but never reports the admin's own typing
     * back out. Fixed on both ends together.
     */
    public function reportAdminTyping(Request $request, $guest_id)
    {
        $this->chat->setAdminTyping($guest_id, $request->boolean('typing'));

        return response()->json(['success' => true]);
    }

    public function adminTypingStatus($guest_id)
    {
        return response()->json(['typing' => $this->chat->adminTypingStatus($guest_id)]);
    }
}
