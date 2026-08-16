<?php

namespace App\Events;

use App\Models\GroupMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Private (not public, unlike NewChatMessage for guest support chat):
 * group membership genuinely needs to be authorized — a random student
 * shouldn't be able to listen to the leaders' channel just by guessing its
 * name, the way a guest's own unguessable session ID protects 1:1 support
 * chat. See routes/channels.php for the two separate authorization paths
 * (Sanctum for leaders, the student session guard for the two student
 * groups) since students don't have API tokens yet.
 */
class NewGroupMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public GroupMessage $groupMessage)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('group.'.str_replace('_', '-', $this->groupMessage->group));
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->groupMessage->id,
            'group'       => $this->groupMessage->group,
            'sender_type' => $this->groupMessage->sender_type,
            'sender_id'   => $this->groupMessage->sender_id,
            'sender_name' => $this->groupMessage->sender_name,
            'message'     => $this->groupMessage->message,
            'created_at'  => $this->groupMessage->created_at,
        ];
    }

    public function broadcastAs(): string
    {
        return 'group-message.sent';
    }
}
