<?php

namespace App\Notifications;

use App\Models\LiveClass;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Spec §31, verbatim:
 *   "Isomo rishya ryatangiye"
 *   "Dars: Aqida"
 *   "Umwigisha: Ustadh ..."
 *   "Tangira ubu"
 * This was the single most explicitly-worded notification requirement in
 * the whole spec, and LiveClassService never sent any notification at
 * all when a class started — no database row, no push, nothing.
 */
class LiveClassStartedNotification extends Notification
{
    use Queueable;

    public function __construct(public LiveClass $liveClass)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $hostName = trim($this->liveClass->host->firstname.' '.$this->liveClass->host->lastname);

        return [
            'type'    => 'live_class',
            'title'   => 'Isomo rishya ryatangiye',
            'message' => "Dars: {$this->liveClass->title} · Umwigisha: {$hostName} · Tangira ubu",
            'url'     => route('student.liveClass.show', $this->liveClass->id),
        ];
    }
}
