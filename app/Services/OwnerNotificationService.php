<?php

namespace App\Services;

use App\Models\Owner;

class OwnerNotificationService
{
    public function paginate(Owner $owner, int $perPage = 20)
    {
        return $owner->notifications()->paginate($perPage);
    }

    public function unreadCount(Owner $owner): int
    {
        return $owner->unreadNotifications()->count();
    }

    public function markRead(Owner $owner, string $id): void
    {
        $owner->notifications()->where('id', $id)->firstOrFail()->markAsRead();
    }

    public function markAllRead(Owner $owner): void
    {
        $owner->unreadNotifications->markAsRead();
    }
}
