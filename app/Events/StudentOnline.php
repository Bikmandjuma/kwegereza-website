<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired exactly once per genuine OFFLINE -> ONLINE transition (see
 * UpdateLastActive), not on every request — the specific gap spec
 * section 17 calls out by name. Private channel: only owners with
 * students.view should be able to watch who's coming online, matching
 * the same permission already gating the Students management feature
 * (Phase 5) rather than exposing this to everyone.
 */
class StudentOnline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $student)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('online-students');
    }

    public function broadcastWith(): array
    {
        return [
            'id'   => $this->student->id,
            'name' => trim($this->student->firstname.' '.$this->student->lastname),
            'at'   => now()->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'student.online';
    }
}
