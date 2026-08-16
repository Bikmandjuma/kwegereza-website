<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookNotification extends Notification
{
    use Queueable;

    public function __construct(public Book $book)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'book',
            'title'   => 'Igitabo Gishya',
            'message' => $this->book->title,
            'url'     => route('guest.books'),
        ];
    }
}
