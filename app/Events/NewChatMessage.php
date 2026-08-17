<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * The first real chat broadcast in the app — previously "Kwegereza Chat"
 * was pure HTTP polling on both the guest widget and the owner inbox
 * (confirmed in the Phase 0 audit) despite laravel-echo/pusher-js being
 * installed since the start. Public, not private: guest_id is an
 * unguessable per-session token and guests are never authenticated at all
 * in this app, so there's no Sanctum/session identity to gate a private
 * channel against on the guest side — the channel name itself is the only
 * thing an eavesdropper would need to guess, matching the existing
 * security model (nothing here is more or less exposed than the current
 * polling endpoints already are).
 */
class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.'.$this->message->guest_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->message->id,
            'guest_id'    => $this->message->guest_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->message->sender_name,
            'message'     => $this->message->message,
            'is_read'     => (bool) $this->message->is_read,
            'read_at'     => $this->message->read_at,
            'created_at'  => $this->message->created_at,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
