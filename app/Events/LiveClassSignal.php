<?php

namespace App\Events;

use App\Models\LiveClass;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * The actual audio/video media in a WebRTC call flows peer-to-peer,
 * never through this server — Laravel's job here is ONLY to relay the
 * signaling handshake (SDP offers/answers, ICE candidates) that peers
 * need to find and negotiate directly with each other, which is exactly
 * what spec section 24 means by "real WebRTC/realtime infrastructure."
 * `to` scopes delivery to one specific participant connection (the
 * signaling channel is shared by everyone in the class, but a given
 * offer/answer/ICE candidate is only relevant to one peer pair).
 */
class LiveClassSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public LiveClass $liveClass,
        public string $fromParticipantKey,
        public string $toParticipantKey,
        public array $signal
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('live-class.'.$this->liveClass->id);
    }

    public function broadcastWith(): array
    {
        return [
            'from' => $this->fromParticipantKey,
            'to' => $this->toParticipantKey,
            'signal' => $this->signal,
        ];
    }

    public function broadcastAs(): string
    {
        return 'signal';
    }
}
