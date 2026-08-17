<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuizController extends Controller
{
    public function __construct(private GamificationService $gamification)
    {
    }
    public function start($id)
    {
        $quiz = Quiz::published()->with('questions.answers')->findOrFail($id);
        $user = Auth::guard('student')->user();

        if (!$quiz->isTakeableNow()) {
            return back()->with('error', 'Iki kizamini ntikirabona gutangira.');
        }

        // Resume an already-in-progress attempt rather than starting a new
        // timer from scratch if the student reloaded or navigated away.
        $attempt = QuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => $user->id, 'submitted_at' => null],
            ['started_at' => now()]
        );

        return redirect()->route('student.quizzes.take', $attempt->id);
    }

    public function take($attemptId)
    {
        $user = Auth::guard('student')->user();

        $attempt = QuizAttempt::with('quiz.questions.answers')
            ->where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        if (!$quiz->isTakeableNow()) {
            return redirect()->route('student.dashboard')->with('error', 'Iki kizamini ntikirabona gutangira.');
        }

        return view('Users.User.quiz-take', compact('quiz', 'attempt'));
    }

    public function submit(Request $request, $attemptId)
    {
        $user = Auth::guard('student')->user();

        $attempt = QuizAttempt::with('quiz.questions.answers')
            ->where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        $score = 0;
        $totalPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $submitted = $request->input('answer_' . $question->id);

            $isCorrect = false;
            $chosenAnswerId = null;
            $answerText = null;

            if ($question->type === 'short_answer') {
                $answerText = trim((string) $submitted);
                $isCorrect = strcasecmp($answerText, trim((string) $question->short_answer)) === 0;
            } else {
                $chosenAnswerId = $submitted;
                $chosen = $question->answers->firstWhere('id', (int) $submitted);
                $isCorrect = $chosen && $chosen->is_correct;
            }

            if ($isCorrect) {
                $score += $question->points;
            }

            QuizAttemptAnswer::create([
                'quiz_attempt_id'  => $attempt->id,
                'quiz_question_id' => $question->id,
                'quiz_answer_id'   => $chosenAnswerId,
                'answer_text'      => $answerText,
                'is_correct'       => $isCorrect,
            ]);
        }

        $percentage = $totalPoints > 0 ? (int) round(($score / $totalPoints) * 100) : 0;
        $passed = $percentage >= $quiz->passing_percentage;

        $attempt->update([
            'score'        => $score,
            'total_points' => $totalPoints,
            'percentage'   => $percentage,
            'passed'       => $passed,
            'submitted_at' => now(),
        ]);

        if ($passed) {
            $this->gamification->recordActivityAndCheckBadges($user);
        }

        return redirect()->route('student.quizzes.result', $attempt->id);
    }

    public function result($attemptId)
    {
        $user = Auth::guard('student')->user();

        $attempt = QuizAttempt::with(['quiz.questions.answers', 'answers'])
            ->where('user_id', $user->id)
            ->findOrFail($attemptId);

        return view('Users.User.quiz-result', compact('attempt'));
    }

    public function history()
    {
        $user = Auth::guard('student')->user();

        $attempts = QuizAttempt::with('quiz')
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->paginate(15);

        $attemptedQuizIds = QuizAttempt::where('user_id', $user->id)->pluck('quiz_id');

        $undone = Quiz::relevantToStudent($user->id)
            ->whereNotIn('id', $attemptedQuizIds)
            ->get();

        return view('Users.User.quiz-history', compact('attempts', 'undone'));
    }
}
