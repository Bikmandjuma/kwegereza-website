<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Regression test for a real, confirmed bug found while building the Quiz
 * API: Quiz::isTakeableNow() only ever checked the overall schedule window
 * (starts_at), never the per-attempt duration_minutes. Nothing anywhere
 * rejected a late submission — a student could start a 10-minute quiz and
 * submit full, deliberated answers three days later. Fixed via
 * Quiz::isAttemptStillWithinDeadline(), enforced server-side in
 * StudentQuizController::submit().
 */
class QuizDurationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private function makeTimedQuiz(int $durationMinutes): Quiz
    {
        $quiz = Quiz::create([
            'title' => 'Timed Quiz', 'passing_percentage' => 50,
            'status' => 'published', 'duration_minutes' => $durationMinutes,
        ]);
        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id, 'question' => 'Is the sky blue?',
            'type' => 'true_false', 'points' => 10, 'order' => 1,
        ]);
        QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'True', 'is_correct' => true, 'order' => 0]);
        QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'False', 'is_correct' => false, 'order' => 1]);

        return $quiz->fresh();
    }

    private function makeStudent(): User
    {
        return User::create([
            'user_code' => 'STD-QZ01', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@quiz.test', 'phone' => '0788111222', 'password' => Hash::make('password123'),
        ]);
    }

    public function test_submitting_within_the_time_limit_is_graded_normally(): void
    {
        $quiz = $this->makeTimedQuiz(10);
        $student = $this->makeStudent();
        $question = $quiz->questions->first();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->actingAs($student, 'student')->post("/student/quizzes/{$quiz->id}/start");
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->firstOrFail();

        // Submit at 5 of 10 allowed minutes — well within the deadline.
        Carbon::setTestNow($attempt->started_at->copy()->addMinutes(5));

        $response = $this->actingAs($student, 'student')->post("/student/quizzes/attempt/{$attempt->id}/submit", [
            "answer_{$question->id}" => $correctAnswer->id,
        ]);

        $response->assertRedirect(route('student.quizzes.result', $attempt->id));
        $this->assertSame(100, $attempt->fresh()->percentage);
        $this->assertTrue($attempt->fresh()->passed);

        Carbon::setTestNow();
    }

    public function test_submitting_after_the_time_limit_is_rejected_and_scored_zero(): void
    {
        $quiz = $this->makeTimedQuiz(10);
        $student = $this->makeStudent();
        $question = $quiz->questions->first();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->actingAs($student, 'student')->post("/student/quizzes/{$quiz->id}/start");
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->firstOrFail();

        // The exact scenario the bug allowed: submit 3 days late with the
        // fully correct answer, expecting full marks.
        Carbon::setTestNow($attempt->started_at->copy()->addDays(3));

        $response = $this->actingAs($student, 'student')->post("/student/quizzes/attempt/{$attempt->id}/submit", [
            "answer_{$question->id}" => $correctAnswer->id,
        ]);

        $response->assertRedirect(route('student.quizzes.result', $attempt->id));
        $fresh = $attempt->fresh();
        $this->assertSame(0, $fresh->percentage, 'A late submission must NOT be graded on its actual answers.');
        $this->assertFalse($fresh->passed);
        $this->assertNotNull($fresh->submitted_at, 'The attempt must be finalized (not left resumable forever) once past deadline.');

        Carbon::setTestNow();
    }

    public function test_a_30_second_grace_period_is_still_graded_normally(): void
    {
        $quiz = $this->makeTimedQuiz(10);
        $student = $this->makeStudent();
        $question = $quiz->questions->first();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->actingAs($student, 'student')->post("/student/quizzes/{$quiz->id}/start");
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->firstOrFail();

        // 15 seconds past the nominal deadline — within the grace window.
        Carbon::setTestNow($attempt->started_at->copy()->addMinutes(10)->addSeconds(15));

        $response = $this->actingAs($student, 'student')->post("/student/quizzes/attempt/{$attempt->id}/submit", [
            "answer_{$question->id}" => $correctAnswer->id,
        ]);

        $this->assertSame(100, $attempt->fresh()->percentage);

        Carbon::setTestNow();
    }

    public function test_untimed_quiz_has_no_deadline_at_all(): void
    {
        $quiz = $this->makeTimedQuiz(0);
        $quiz->update(['duration_minutes' => null]);
        $student = $this->makeStudent();
        $question = $quiz->questions->first();
        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        $this->actingAs($student, 'student')->post("/student/quizzes/{$quiz->id}/start");
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->firstOrFail();

        Carbon::setTestNow($attempt->started_at->copy()->addDays(30));

        $this->actingAs($student, 'student')->post("/student/quizzes/attempt/{$attempt->id}/submit", [
            "answer_{$question->id}" => $correctAnswer->id,
        ]);

        $this->assertSame(100, $attempt->fresh()->percentage, 'A quiz with no duration_minutes must never be time-limited.');

        Carbon::setTestNow();
    }
}
