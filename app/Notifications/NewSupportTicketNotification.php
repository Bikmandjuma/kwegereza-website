<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * The first owner-facing notification in the app — every existing
 * Notification class (NewAmatangazoNotification, NewDarsatNotification,
 * QuizScheduledNotification) is sent TO students, never to owners. Owner
 * and User both already `use Notifiable`, and the notifications table is
 * Laravel's standard polymorphic schema, so this works without any schema
 * change — the gap was simply that nothing had ever triggered one.
 */
class NewSupportTicketNotification extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'support_ticket',
            'title'   => 'Ikibazo gishya cy\'ubufasha',
            'message' => $this->ticket->subject,
            'url'     => route('owner.support.show', $this->ticket->id),
        ];
    }

    /**
     * Real-time push, on top of the database row above — the first actual
     * broadcast event in the whole app (confirmed absent in the Phase 0
     * audit: Pusher/Echo were installed but never wired to a single
     * event). Sent on the private per-owner channel from routes/channels.php,
     * so only that specific owner's own connected browser receives it.
     */
    public function toBroadcast($notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toDatabase($notifiable));
    }
}
