<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\DarsatProgress;
use App\Models\DarsatTable;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Notifications\QuizScheduledNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class QuizService
{
    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = null)
    {
        return Quiz::withCount(['questions', 'attempts'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Quiz
    {
        return Quiz::withCount(['questions', 'attempts'])->findOrFail($id);
    }

    public function findWithQuestions(int $id): Quiz
    {
        return Quiz::with('questions.answers')->findOrFail($id);
    }

    /**
     * Parses the admin's "Kizatangira ryari?" datetime-local input as
     * Africa/Kigali time and converts to UTC — see the historical bug this
     * fixed, documented on the original QuizController method.
     */
    public function parseStartsAt(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d\TH:i', $value, 'Africa/Kigali')->setTimezone('UTC');
    }

    public function create(array $data, int $ownerId): Quiz
    {
        $quizzableType = null;
        $quizzableId = null;

        if (($data['attach_type'] ?? null) === 'course_lesson' && ($data['attach_id'] ?? null)) {
            $quizzableType = CourseLesson::class;
            $quizzableId = $data['attach_id'];
        } elseif (($data['attach_type'] ?? null) === 'darsat' && ($data['attach_id'] ?? null)) {
            $quizzableType = DarsatTable::class;
            $quizzableId = $data['attach_id'];
        }

        $quiz = Quiz::create([
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'quizzable_type'     => $quizzableType,
            'quizzable_id'       => $quizzableId,
            'passing_percentage' => $data['passing_percentage'],
            'status'             => $data['status'],
            'starts_at'          => $this->parseStartsAt($data['starts_at'] ?? null),
            'duration_minutes'   => $data['duration_minutes'] ?? null,
            'created_by'         => $ownerId,
        ]);

        $this->notifyRelevantStudentsIfScheduled($quiz);
        $this->broadcastPushIfScheduled($quiz);

        return $quiz;
    }

    public function update(Quiz $quiz, array $data, int $ownerId): Quiz
    {
        $previousStartsAt = $quiz->starts_at;

        $quiz->update([
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'passing_percentage' => $data['passing_percentage'],
            'status'             => $data['status'],
            'starts_at'          => $this->parseStartsAt($data['starts_at'] ?? null),
            'duration_minutes'   => $data['duration_minutes'] ?? null,
            'updated_by'         => $ownerId,
        ]);

        $newStartsAt = $this->parseStartsAt($data['starts_at'] ?? null);
        if ($newStartsAt && (! $previousStartsAt || ! $previousStartsAt->equalTo($newStartsAt))) {
            $this->notifyRelevantStudentsIfScheduled($quiz->fresh());
            $this->broadcastPushIfScheduled($quiz->fresh());
        }

        return $quiz;
    }

    public function delete(Quiz $quiz): void
    {
        $quiz->delete();
    }

    public function createQuestion(Quiz $quiz, array $data): QuizQuestion
    {
        $nextOrder = ($quiz->questions()->max('order') ?? 0) + 1;

        $question = QuizQuestion::create([
            'quiz_id'            => $quiz->id,
            'question'           => $data['question'],
            'type'               => $data['type'],
            'short_answer'       => $data['type'] === 'short_answer' ? ($data['short_answer'] ?? null) : null,
            'points'             => $data['points'],
            'order'              => $nextOrder,
            'time_limit_seconds' => $data['time_limit_seconds'] ?? null,
        ]);

        if ($data['type'] === 'multiple_choice') {
            foreach ($data['answers'] ?? [] as $index => $text) {
                if (trim((string) $text) === '') {
                    continue;
                }
                QuizAnswer::create([
                    'quiz_question_id' => $question->id,
                    'answer_text'      => $text,
                    'is_correct'       => (string) ($data['correct'] ?? null) === (string) $index,
                    'order'            => $index,
                ]);
            }
        } elseif ($data['type'] === 'true_false') {
            QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'True', 'is_correct' => ($data['correct'] ?? null) === 'true', 'order' => 0]);
            QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'False', 'is_correct' => ($data['correct'] ?? null) === 'false', 'order' => 1]);
        }

        return $question;
    }

    public function updateQuestion(QuizQuestion $question, array $data): QuizQuestion
    {
        $question->update([
            'question'           => $data['question'],
            'points'             => $data['points'],
            'time_limit_seconds' => $data['time_limit_seconds'] ?? null,
        ]);

        return $question;
    }

    public function deleteQuestion(QuizQuestion $question): void
    {
        $question->delete();
    }

    private function broadcastPushIfScheduled(Quiz $quiz): void
    {
        if (! $quiz->starts_at || $quiz->starts_at->isPast() || $quiz->status !== 'published') {
            return;
        }

        $whenLocal = $quiz->starts_at->copy()->setTimezone('Africa/Kigali')->format('M j, g:i A');

        app(PushNotificationService::class)->sendToAll(
            'Ikizamini Gishya: '.$quiz->title,
            'Kizatangira '.$whenLocal.'. Kanda urebe.',
            route('student.quizzes.history')
        );
    }

    private function notifyRelevantStudentsIfScheduled(Quiz $quiz): void
    {
        if (! $quiz->starts_at || $quiz->starts_at->isPast() || $quiz->status !== 'published') {
            return;
        }

        $students = collect();

        if ($quiz->quizzable_type === CourseLesson::class) {
            $lesson = CourseLesson::find($quiz->quizzable_id);
            if ($lesson) {
                $userIds = CourseEnrollment::where('course_id', $lesson->course_id)->pluck('user_id');
                $students = User::whereIn('id', $userIds)->get();
            }
        } elseif ($quiz->quizzable_type === DarsatTable::class) {
            $userIds = DarsatProgress::where('darsat_id', $quiz->quizzable_id)->pluck('user_id');
            $students = User::whereIn('id', $userIds)->get();
        }

        if ($students->isNotEmpty()) {
            Notification::send($students, new QuizScheduledNotification($quiz));
        }
    }
}
