<?php

namespace App\Services;

use App\Events\GroupMessageUpdated;
use App\Events\NewGroupMessage;
use App\Models\GroupChatMute;
use App\Models\GroupMessage;
use App\Models\GroupMessageReaction;
use App\Models\GroupMessageReport;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GroupChatService
{
    /**
     * Which fixed group a student belongs to, based on gender — per spec
     * section 19 ("male student group, female student group"). Not
     * configurable per-student; derived directly from their profile.
     */
    public function studentGroupFor(User $student): ?string
    {
        return match (strtolower((string) $student->gender)) {
            'male' => GroupMessage::GROUP_MALE_STUDENTS,
            'female' => GroupMessage::GROUP_FEMALE_STUDENTS,
            default => null,
        };
    }

    public function messagesFor(string $group, int $limit = 100)
    {
        return GroupMessage::forGroup($group)
            ->with(['parent', 'reactions'])
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function pinnedFor(string $group)
    {
        return GroupMessage::forGroup($group)->pinned()->latest('pinned_at')->get();
    }

    /**
     * §27: muted members must not be able to post. Checked before every
     * send, for both students and leaders — moderation applies to anyone.
     */
    public function isMuted(string $group, string $actorType, int $actorId): bool
    {
        $mute = GroupChatMute::where('group', $group)
            ->where('actor_type', $actorType)
            ->where('actor_id', $actorId)
            ->first();

        return $mute && $mute->isActive();
    }

    public function postAsStudent(string $group, User $student, string $message, ?int $parentId = null): GroupMessage
    {
        if ($this->isMuted($group, 'student', $student->id)) {
            throw new \RuntimeException('Waraciwe ijambo muri iki kiganiro.');
        }

        $groupMessage = GroupMessage::create([
            'group'       => $group,
            'sender_type' => 'student',
            'sender_id'   => $student->id,
            'sender_name' => trim($student->firstname.' '.$student->lastname),
            'message'     => $message,
            'parent_id'   => $this->validParentId($group, $parentId),
        ]);

        $this->broadcastNew($groupMessage);

        return $groupMessage;
    }

    public function postAsOwner(string $group, Owner $owner, string $message, ?int $parentId = null): GroupMessage
    {
        if ($this->isMuted($group, 'owner', $owner->id)) {
            throw new \RuntimeException('Waraciwe ijambo muri iki kiganiro.');
        }

        $groupMessage = GroupMessage::create([
            'group'       => $group,
            'sender_type' => 'owner',
            'sender_id'   => $owner->id,
            'sender_name' => trim($owner->firstname.' '.$owner->lastname),
            'message'     => $message,
            'parent_id'   => $this->validParentId($group, $parentId),
        ]);

        $this->broadcastNew($groupMessage);

        return $groupMessage;
    }

    /** A reply's parent must actually exist in the same group — otherwise silently post as a top-level message. */
    private function validParentId(string $group, ?int $parentId): ?int
    {
        if (! $parentId) {
            return null;
        }

        return GroupMessage::forGroup($group)->whereKey($parentId)->exists() ? $parentId : null;
    }

    /**
     * Toggle: reacting with the same emoji again removes it (standard
     * chat-app behavior), rather than stacking duplicate reactions.
     */
    public function react(GroupMessage $message, string $actorType, int $actorId, string $emoji): string
    {
        $existing = GroupMessageReaction::where('group_message_id', $message->id)
            ->where('actor_type', $actorType)
            ->where('actor_id', $actorId)
            ->where('emoji', $emoji)
            ->first();

        if ($existing) {
            $existing->delete();
            $type = 'unreacted';
        } else {
            GroupMessageReaction::create([
                'group_message_id' => $message->id,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
                'emoji' => $emoji,
            ]);
            $type = 'reacted';
        }

        $this->broadcastUpdate($message->group, $type, [
            'message_id' => $message->id,
            'emoji' => $emoji,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'reactions' => $this->reactionSummary($message->fresh()),
        ]);

        return $type;
    }

    /** Grouped counts per emoji, e.g. {"👍": 3, "❤️": 1} — cheaper to send over the wire than every individual reaction row. */
    public function reactionSummary(GroupMessage $message): array
    {
        return $message->reactions()
            ->selectRaw('emoji, count(*) as count')
            ->groupBy('emoji')
            ->pluck('count', 'emoji')
            ->toArray();
    }

    /**
     * Soft delete only — §28 explicitly forbids permanent deletion on the
     * spot. Anyone can delete their own message; moderation (deleting
     * someone else's) is gated by the caller checking a permission before
     * calling this with $isModerator = true.
     */
    public function delete(GroupMessage $message, string $actorType, int $actorId, bool $isModerator = false): void
    {
        $isOwnMessage = $message->sender_type === $actorType && $message->sender_id === $actorId;

        if (! $isOwnMessage && ! $isModerator) {
            throw new \RuntimeException('Ntushobora gusiba ubu butumwa.');
        }

        $message->delete();

        $this->broadcastUpdate($message->group, 'deleted', ['message_id' => $message->id]);
    }

    public function pin(GroupMessage $message, Owner $owner): void
    {
        $message->update([
            'pinned_at' => now(),
            'pinned_by_type' => 'owner',
            'pinned_by_id' => $owner->id,
        ]);

        $this->broadcastUpdate($message->group, 'pinned', ['message_id' => $message->id]);
    }

    public function unpin(GroupMessage $message): void
    {
        $message->update(['pinned_at' => null, 'pinned_by_type' => null, 'pinned_by_id' => null]);

        $this->broadcastUpdate($message->group, 'unpinned', ['message_id' => $message->id]);
    }

    public function report(GroupMessage $message, string $reporterType, int $reporterId, ?string $reason): GroupMessageReport
    {
        return GroupMessageReport::create([
            'group_message_id' => $message->id,
            'reporter_type' => $reporterType,
            'reporter_id' => $reporterId,
            'reason' => $reason,
        ]);
    }

    public function mute(string $group, string $actorType, int $actorId, Owner $mutedBy, ?\DateTimeInterface $until = null): GroupChatMute
    {
        return GroupChatMute::updateOrCreate(
            ['group' => $group, 'actor_type' => $actorType, 'actor_id' => $actorId],
            ['muted_by' => $mutedBy->id, 'muted_until' => $until]
        );
    }

    public function unmute(string $group, string $actorType, int $actorId): void
    {
        GroupChatMute::where('group', $group)
            ->where('actor_type', $actorType)
            ->where('actor_id', $actorId)
            ->delete();
    }

    /**
     * Retention cleanup — spec §28. Two stages, both configurable
     * (config/group_chat.php), run from the console command registered
     * in the scheduler. Soft-deletes old messages first; only actually
     * purges (hard-deletes) messages that have ALREADY been soft-deleted
     * for a further configurable period. Either stage can be disabled by
     * setting its config value to null.
     */
    public function runRetentionCleanup(): array
    {
        $softDeleteAfterDays = config('group_chat.soft_delete_after_days');
        $purgeAfterDays = config('group_chat.purge_after_days');

        $softDeleted = 0;
        if ($softDeleteAfterDays !== null) {
            $softDeleted = GroupMessage::where('created_at', '<', now()->subDays((int) $softDeleteAfterDays))
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }

        $purged = 0;
        if ($purgeAfterDays !== null) {
            $purged = GroupMessage::onlyTrashed()
                ->where('deleted_at', '<', now()->subDays((int) $purgeAfterDays))
                ->forceDelete();
        }

        return ['soft_deleted' => $softDeleted, 'purged' => $purged];
    }

    private function broadcastNew(GroupMessage $groupMessage): void
    {
        try {
            broadcast(new NewGroupMessage($groupMessage));
        } catch (\Throwable $e) {
            Log::warning('Group message broadcast failed (message was still saved).', [
                'group_message_id' => $groupMessage->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    private function broadcastUpdate(string $group, string $type, array $payload): void
    {
        try {
            broadcast(new GroupMessageUpdated($group, $type, $payload));
        } catch (\Throwable $e) {
            Log::warning('Group message update broadcast failed.', [
                'group' => $group,
                'type' => $type,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
