<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizScheduledNotification extends Notification
{
    use Queueable;

    public function __construct(public Quiz $quiz)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'quiz_scheduled',
            'title'   => 'Ikizamini giteganyijwe',
            'message' => $this->quiz->title . ' — kizatangira ku ya ' . $this->quiz->starts_at->format('M j, g:i A'),
            'url'     => route('student.quizzes.history'),
        ];
    }
}
