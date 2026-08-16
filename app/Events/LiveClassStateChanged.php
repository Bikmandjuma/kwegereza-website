<?php

namespace App\Events;

use App\Models\LiveClass;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Covers every non-media state change in the classroom (participant
 * joined/left, hand raised/lowered, muted/unmuted, removed, class
 * ended) as one generic event with a `type` discriminator, rather than a
 * separate broadcast class per action — the payload shape is identical
 * for all of them (who, what changed), so a family of near-duplicate
 * event classes wouldn't add anything real.
 */
class LiveClassStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public LiveClass $liveClass,
        public string $type,
        public array $payload = []
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('live-class.'.$this->liveClass->id);
    }

    public function broadcastWith(): array
    {
        return array_merge(['type' => $this->type], $this->payload);
    }

    public function broadcastAs(): string
    {
        return 'state-changed';
    }
}
