<?php

namespace App\Services;

use App\Models\Amatangazo;
use App\Models\User;
use App\Notifications\NewAmatangazoNotification;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class AnnouncementService
{
    use HandlesFileUploads;

    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = null)
    {
        return Amatangazo::when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->status($status)
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Amatangazo
    {
        return Amatangazo::findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image, int $ownerId): Amatangazo
    {
        $announcement = Amatangazo::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'presenter'    => $data['presenter'] ?? null,
            'status'       => $data['status'],
            'image'        => $this->storeUploadedFile($image, 'amatangazo'),
            'is_published' => $data['is_published'] ?? true,
            'published_at' => now(),
            'created_by'   => $ownerId,
        ]);

        if ($announcement->is_published) {
            User::whereNotNull('id')->chunk(200, function ($students) use ($announcement) {
                Notification::send($students, new NewAmatangazoNotification($announcement));
            });

            // Same gap as Darsat — database-only before, never an actual
            // push notification despite spec §32 listing "announcement"
            // explicitly as a push-notification type.
            app(PushNotificationService::class)->sendToAll(
                'Itangazo Rishya',
                $announcement->title,
                route('guest.news')
            );
        }

        return $announcement;
    }

    /**
     * Note: matches the existing web form's behavior of accepting an
     * explicit 'is_published' value on update (unlike the pre-extraction
     * code, which defaulted a missing checkbox field to `true` via
     * `$request->boolean('is_published', true)` — meaning editing an
     * unpublished announcement without re-checking the box would silently
     * re-publish it). The service now requires the caller to pass the
     * current/intended value explicitly rather than guessing a default,
     * so the API can't reproduce that same accidental-republish behavior.
     */
    public function update(Amatangazo $announcement, array $data, ?UploadedFile $image, int $ownerId): Amatangazo
    {
        $imageName = $announcement->image;
        if ($image) {
            $this->deleteUploadedFile($announcement->image, 'amatangazo');
            $imageName = $this->storeUploadedFile($image, 'amatangazo');
        }

        $announcement->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'presenter'    => $data['presenter'] ?? null,
            'status'       => $data['status'],
            'image'        => $imageName,
            'is_published' => $data['is_published'] ?? $announcement->is_published,
            'updated_by'   => $ownerId,
        ]);

        return $announcement;
    }

    public function delete(Amatangazo $announcement): void
    {
        $this->deleteUploadedFile($announcement->image, 'amatangazo');
        $announcement->delete();
    }

    public function togglePublish(Amatangazo $announcement): Amatangazo
    {
        $announcement->update(['is_published' => ! $announcement->is_published]);

        return $announcement;
    }
}
