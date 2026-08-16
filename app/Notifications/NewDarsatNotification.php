<?php

namespace App\Notifications;

use App\Models\DarsatTable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDarsatNotification extends Notification
{
    use Queueable;

    public function __construct(public DarsatTable $darsat)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'darsat',
            'title'   => 'Isomo rishya',
            'message' => $this->darsat->title,
            'url'     => route('guest.teacher-darsa', $this->darsat->teachers),
        ];
    }
}
