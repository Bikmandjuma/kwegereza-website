<?php

namespace App\Services;

use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use App\Models\ChatPresence;
use Illuminate\Support\Facades\Log;

class ChatService
{
    public function conversations()
    {
        $guests = ChatMessage::select('guest_id', 'sender_name')
            ->groupBy('guest_id', 'sender_name')
            ->get();

        return $guests->map(function ($guest) {
            $lastMessage = ChatMessage::where('guest_id', $guest->guest_id)->latest('id')->first();
            $guest->last_message = $lastMessage?->message;
            $guest->last_message_at = $lastMessage?->created_at;

            $guest->unread_count = ChatMessage::where('guest_id', $guest->guest_id)
                ->where('sender_type', 'guest')
                ->where('is_read', false)
                ->count();

            $presence = ChatPresence::where('guest_id', $guest->guest_id)->first();
            $guest->online = $presence && now()->diffInSeconds($presence->last_seen) < 20;
            $guest->typing = (bool) ($presence->typing ?? false);

            return $guest;
        })->sortByDesc('last_message_at')->values();
    }

    public function messagesFor(string $guestId)
    {
        return ChatMessage::where('guest_id', $guestId)->orderBy('id')->get();
    }

    public function sendAsAdmin(string $guestId, string $message, string $adminName): ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'guest_id'    => $guestId,
            'sender_type' => 'admin',
            'sender_name' => $adminName,
            'message'     => $message,
        ]);

        $this->broadcast($chatMessage);

        return $chatMessage;
    }

    public function sendAsGuest(string $guestId, string $message, string $guestName): ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'guest_id'    => $guestId,
            'sender_type' => 'guest',
            'sender_name' => $guestName,
            'message'     => $message,
        ]);

        $this->broadcast($chatMessage);

        return $chatMessage;
    }

    public function markRead(string $guestId): void
    {
        ChatMessage::where('guest_id', $guestId)
            ->where('sender_type', 'guest')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function touchPresence(string $guestId): void
    {
        ChatPresence::updateOrCreate(['guest_id' => $guestId], ['last_seen' => now()]);
    }

    /**
     * Guest reporting their own typing — read by the admin/owner chatroom
     * via guestTypingStatus(). The route the real guest widget actually
     * calls is POST /chat/typing with {guest_id, typing} in the body (not
     * a {guest_id} route segment) — matching that exactly rather than the
     * shape I first assumed.
     */
    public function setGuestTyping(string $guestId, bool $typing): void
    {
        ChatPresence::updateOrCreate(['guest_id' => $guestId], ['typing' => $typing, 'last_seen' => now()]);
    }

    public function guestTypingStatus(string $guestId): bool
    {
        return (bool) (ChatPresence::where('guest_id', $guestId)->value('typing') ?? false);
    }

    /**
     * The other direction — admin reporting their own typing back to the
     * guest. The guest widget already polls GET /chat/admin-typing/{id}
     * for this (confirmed by reading twandikire.blade.php directly), but
     * nothing on the admin side ever wrote it and the route didn't exist
     * at all. Both fixed together since one is useless without the other.
     */
    public function setAdminTyping(string $guestId, bool $typing): void
    {
        ChatPresence::updateOrCreate(['guest_id' => $guestId], ['admin_typing' => $typing, 'last_seen' => now()]);
    }

    public function adminTypingStatus(string $guestId): bool
    {
        return (bool) (ChatPresence::where('guest_id', $guestId)->value('admin_typing') ?? false);
    }

    /**
     * Same resilience principle as the Notifications phase's broadcast
     * fix: a message must always save even if the realtime push fails, so
     * a broadcast/WebSocket outage never breaks the ability to actually
     * chat — it would just fall back silently to needing a refresh.
     */
    private function broadcast(ChatMessage $message): void
    {
        try {
            broadcast(new NewChatMessage($message));
        } catch (\Throwable $e) {
            Log::warning('Chat message broadcast failed (message was still saved).', [
                'message_id' => $message->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
