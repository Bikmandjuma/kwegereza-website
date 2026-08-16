<?php

namespace App\Services;

use App\Events\LiveClassSignal;
use App\Events\LiveClassStateChanged;
use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Models\Owner;
use App\Models\User;
use App\Notifications\LiveClassStartedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class LiveClassService
{
    public function create(string $title, Owner $host, ?\Illuminate\Support\Carbon $scheduledAt = null): LiveClass
    {
        return LiveClass::create([
            'title' => $title,
            'host_id' => $host->id,
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    public function start(LiveClass $class): LiveClass
    {
        $class->update(['status' => 'live', 'started_at' => now()]);
        $class->loadMissing('host');

        // Was previously silent — starting a class notified nobody at
        // all. Database notification (shows in every student's bell) +
        // a real browser push, matching spec §31's exact wording and
        // required delivery channels.
        $this->notifyClassStarted($class);

        return $class;
    }

    private function notifyClassStarted(LiveClass $class): void
    {
        $hostName = trim($class->host->firstname.' '.$class->host->lastname);

        User::whereNotNull('id')->chunk(200, function ($students) use ($class) {
            Notification::send($students, new LiveClassStartedNotification($class));
        });

        try {
            app(PushNotificationService::class)->sendToAll(
                'Isomo rishya ryatangiye',
                "Dars: {$class->title} · Umwigisha: {$hostName}",
                route('student.liveClass.show', $class->id)
            );
        } catch (\Throwable $e) {
            Log::warning('Live class start push notification failed.', [
                'live_class_id' => $class->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function end(LiveClass $class): LiveClass
    {
        $class->update(['status' => 'ended', 'ended_at' => now()]);
        $class->activeParticipants()->update(['left_at' => now()]);

        $this->broadcast($class, 'class-ended');

        return $class;
    }

    /**
     * Spec section 24: "Default microphone: MUTED." Every participant
     * (student or owner) joins muted unless they are the host — the host
     * needs to be heard from the start or there's no class.
     */
    public function join(LiveClass $class, string $participantType, int $participantId): LiveClassParticipant
    {
        $isHost = $participantType === 'owner' && $participantId === $class->host_id;

        $participant = LiveClassParticipant::updateOrCreate(
            ['live_class_id' => $class->id, 'participant_type' => $participantType, 'participant_id' => $participantId],
            ['role' => $isHost ? 'host' : 'listener', 'is_muted' => ! $isHost, 'joined_at' => now(), 'left_at' => null]
        );

        $this->broadcast($class, 'participant-joined', [
            'participant' => $this->present($participant),
        ]);

        return $participant;
    }

    public function leave(LiveClassParticipant $participant): void
    {
        $participant->update(['left_at' => now()]);

        $this->broadcast($participant->liveClass, 'participant-left', [
            'participant_key' => $this->key($participant),
        ]);
    }

    public function raiseHand(LiveClassParticipant $participant, bool $raised = true): LiveClassParticipant
    {
        $participant->update(['hand_raised' => $raised]);

        $this->broadcast($participant->liveClass, 'hand-raised', [
            'participant_key' => $this->key($participant),
            'raised' => $raised,
        ]);

        return $participant;
    }

    /**
     * Host approves a raised hand — promotes to speaker and unmutes.
     * Spec: "Host can: approve raised hand, allow microphone."
     */
    public function approveHand(LiveClassParticipant $participant): LiveClassParticipant
    {
        $participant->update(['role' => 'speaker', 'is_muted' => false, 'hand_raised' => false]);

        $this->broadcast($participant->liveClass, 'hand-approved', [
            'participant_key' => $this->key($participant),
        ]);

        return $participant;
    }

    public function rejectHand(LiveClassParticipant $participant): LiveClassParticipant
    {
        $participant->update(['hand_raised' => false]);

        $this->broadcast($participant->liveClass, 'hand-rejected', [
            'participant_key' => $this->key($participant),
        ]);

        return $participant;
    }

    public function muteParticipant(LiveClassParticipant $participant): LiveClassParticipant
    {
        $participant->update(['is_muted' => true]);

        $this->broadcast($participant->liveClass, 'participant-muted', [
            'participant_key' => $this->key($participant),
        ]);

        return $participant;
    }

    /**
     * Spec: "Host can: mute everyone." The host itself is deliberately
     * excluded — muting the host would silence the class with no way for
     * anyone to un-mute them again short of leaving and rejoining.
     */
    public function muteEveryone(LiveClass $class): void
    {
        $class->activeParticipants()->where('role', '!=', 'host')->update(['is_muted' => true]);

        $this->broadcast($class, 'muted-everyone');
    }

    public function removeParticipant(LiveClassParticipant $participant): void
    {
        $participant->update(['left_at' => now()]);

        $this->broadcast($participant->liveClass, 'participant-removed', [
            'participant_key' => $this->key($participant),
        ]);
    }

    public function activeParticipants(LiveClass $class)
    {
        return $class->activeParticipants()->get()->map(fn ($p) => $this->present($p));
    }

    /**
     * Relays a WebRTC signaling payload (SDP offer/answer or an ICE
     * candidate) from one participant to a specific other — the actual
     * media negotiation happens directly between browsers once they have
     * enough of these to establish a peer connection; this method never
     * touches or understands the media itself.
     */
    public function relaySignal(LiveClass $class, string $fromKey, string $toKey, array $signal): void
    {
        $this->broadcastSignal($class, $fromKey, $toKey, $signal);
    }

    private function key(LiveClassParticipant $participant): string
    {
        return $participant->participant_type.':'.$participant->participant_id;
    }

    private function present(LiveClassParticipant $participant): array
    {
        return [
            'key' => $this->key($participant),
            'participant_type' => $participant->participant_type,
            'participant_id' => $participant->participant_id,
            'name' => $participant->name(),
            'role' => $participant->role,
            'hand_raised' => $participant->hand_raised,
            'is_muted' => $participant->is_muted,
        ];
    }

    private function broadcast(LiveClass $class, string $type, array $payload = []): void
    {
        try {
            broadcast(new LiveClassStateChanged($class, $type, $payload));
        } catch (\Throwable $e) {
            Log::warning('LiveClass broadcast failed.', ['live_class_id' => $class->id, 'type' => $type, 'exception' => $e->getMessage()]);
        }
    }

    private function broadcastSignal(LiveClass $class, string $from, string $to, array $signal): void
    {
        try {
            broadcast(new LiveClassSignal($class, $from, $to, $signal));
        } catch (\Throwable $e) {
            Log::warning('LiveClass signal broadcast failed.', ['live_class_id' => $class->id, 'exception' => $e->getMessage()]);
        }
    }
}
