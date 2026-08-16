<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Covers every group-message change that ISN'T a brand new message
 * (that's NewGroupMessage): reactions added/removed, pin/unpin, and
 * delete. One event with a `type` discriminator rather than four
 * near-identical broadcast classes, since the client-side handling is
 * the same shape every time — "here's the message id, here's what
 * changed about it."
 */
class GroupMessageUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $group,
        public string $type, // 'reacted' | 'unreacted' | 'pinned' | 'unpinned' | 'deleted'
        public array $payload,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('group.'.str_replace('_', '-', $this->group));
    }

    public function broadcastWith(): array
    {
        return array_merge(['type' => $this->type], $this->payload);
    }

    public function broadcastAs(): string
    {
        return 'group-message.updated';
    }
}
