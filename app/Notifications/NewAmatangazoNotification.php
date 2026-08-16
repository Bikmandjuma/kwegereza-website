<?php

namespace App\Notifications;

use App\Models\Amatangazo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAmatangazoNotification extends Notification
{
    use Queueable;

    public function __construct(public Amatangazo $amatangazo)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'amatangazo',
            'title'   => 'Itangazo rishya',
            'message' => $this->amatangazo->title,
            'url'     => route('guest.news'),
        ];
    }
}
