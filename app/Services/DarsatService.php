<?php

namespace App\Services;

use App\Models\DarsatTable;
use App\Models\User;
use App\Notifications\NewDarsatNotification;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

/**
 * Shared Darsat (lesson recording) business logic — extracted from
 * AdminController exactly the way CourseService was extracted from
 * CourseController in Phase 2, so the Blade admin panel and the new
 * Api\Owner\DarsatController run through one set of rules, not two.
 */
class DarsatService
{
    use HandlesFileUploads;

    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = null, ?int $teacherId = null)
    {
        return DarsatTable::with('teacher')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($teacherId, fn ($q) => $q->where('teachers', $teacherId))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): DarsatTable
    {
        return DarsatTable::with('teacher')->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $audio, ?UploadedFile $thumbnail, int $ownerId): DarsatTable
    {
        $darsat = DarsatTable::create([
            'title'        => $data['title'],
            'teachers'     => $data['teachers'],
            'type'         => $data['type'],
            'description'  => $data['description'] ?? null,
            'audio'        => $this->storeUploadedFile($audio, 'audio'),
            'thumbnail'    => $this->storeUploadedFile($thumbnail, 'darsat/thumbnails'),
            'status'       => $data['status'],
            'created_by'   => $ownerId,
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        if ($darsat->status === 'published') {
            User::whereNotNull('id')->chunk(200, function ($students) use ($darsat) {
                Notification::send($students, new NewDarsatNotification($darsat));
            });

            // Was database-only before — a new Dars never actually reached
            // anyone as a real browser push notification despite the app
            // having a fully working PushNotificationService (used
            // elsewhere for quizzes) sitting unused for this. Spec §32
            // explicitly lists "new lesson" as a push-notification type.
            app(PushNotificationService::class)->sendToAll(
                'Isomo Rishya',
                $darsat->title,
                route('guest.teacher-darsa', $darsat->teachers)
            );
        }

        return $darsat;
    }

    public function update(DarsatTable $darsat, array $data, ?UploadedFile $audio, ?UploadedFile $thumbnail, int $ownerId): DarsatTable
    {
        $audioName = $darsat->audio;
        if ($audio) {
            $this->deleteUploadedFile($darsat->audio, 'audio');
            $audioName = $this->storeUploadedFile($audio, 'audio');
        }

        $thumbnailName = $darsat->thumbnail;
        if ($thumbnail) {
            $this->deleteUploadedFile($darsat->thumbnail, 'darsat/thumbnails');
            $thumbnailName = $this->storeUploadedFile($thumbnail, 'darsat/thumbnails');
        }

        $darsat->update([
            'title'        => $data['title'],
            'teachers'     => $data['teachers'],
            'type'         => $data['type'],
            'description'  => $data['description'] ?? null,
            'audio'        => $audioName,
            'thumbnail'    => $thumbnailName,
            'status'       => $data['status'],
            'updated_by'   => $ownerId,
            'published_at' => $data['status'] === 'published' ? ($darsat->published_at ?? now()) : null,
        ]);

        return $darsat;
    }

    public function delete(DarsatTable $darsat): void
    {
        $this->deleteUploadedFile($darsat->audio, 'audio');
        $this->deleteUploadedFile($darsat->thumbnail, 'darsat/thumbnails');
        $darsat->delete();
    }
}
