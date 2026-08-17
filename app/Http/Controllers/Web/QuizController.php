<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\DarsatTable;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\PushNotificationService;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:quizzes.view')->only(['index', 'show']);
        $this->middleware('permission:quizzes.create')->only(['store', 'storeQuestion']);
        $this->middleware('permission:quizzes.update')->only(['update', 'updateQuestion']);
        $this->middleware('permission:quizzes.delete')->only(['destroy', 'destroyQuestion']);
    }

    public function index()
    {
        $quizzes = Quiz::withCount(['questions', 'attempts'])->latest()->paginate(10);
        $courseLessons = CourseLesson::with('course')->orderBy('title')->get();
        $darsatLessons = DarsatTable::published()->orderBy('title')->get();

        return view('Users.admin.quizzes', compact('quizzes', 'courseLessons', 'darsatLessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'attach_type'         => 'nullable|in:course_lesson,darsat,none',
            'attach_id'           => 'nullable|integer',
            'passing_percentage'  => 'required|integer|min:1|max:100',
            'status'              => 'required|in:draft,published',
            'starts_at'           => 'nullable|date',
            'duration_minutes'    => 'nullable|integer|min:1|max:600',
        ]);

        $quizzableType = null;
        $quizzableId = null;

        if ($request->attach_type === 'course_lesson' && $request->attach_id) {
            $quizzableType = CourseLesson::class;
            $quizzableId = $request->attach_id;
        } elseif ($request->attach_type === 'darsat' && $request->attach_id) {
            $quizzableType = DarsatTable::class;
            $quizzableId = $request->attach_id;
        }

        $quiz = Quiz::create([
            'title'               => $request->title,
            'description'         => $request->description,
            'quizzable_type'      => $quizzableType,
            'quizzable_id'        => $quizzableId,
            'passing_percentage'  => $request->passing_percentage,
            'status'              => $request->status,
            'starts_at'           => $this->parseStartsAt($request->starts_at),
            'duration_minutes'    => $request->duration_minutes,
            'created_by'          => auth('owner')->id(),
        ]);

        $this->notifyRelevantStudentsIfScheduled($quiz);
        $this->broadcastPushIfScheduled($quiz);

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'status'             => 'required|in:draft,published',
            'starts_at'          => 'nullable|date',
            'duration_minutes'   => 'nullable|integer|min:1|max:600',
        ]);

        $previousStartsAt = $quiz->starts_at;

        $quiz->update([
            'title'              => $request->title,
            'description'        => $request->description,
            'passing_percentage' => $request->passing_percentage,
            'status'             => $request->status,
            'starts_at'          => $this->parseStartsAt($request->starts_at),
            'duration_minutes'   => $request->duration_minutes,
            'updated_by'         => auth('owner')->id(),
        ]);

        // Only notify if a schedule was newly added or changed — not on
        // every unrelated edit (e.g. fixing a typo in the title).
        $newStartsAt = $this->parseStartsAt($request->starts_at);

        if ($newStartsAt && (!$previousStartsAt || !$previousStartsAt->equalTo($newStartsAt))) {
            $this->notifyRelevantStudentsIfScheduled($quiz->fresh());
            $this->broadcastPushIfScheduled($quiz->fresh());
        }

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyahinduwe.');
    }

    /**
     * Notify the students this quiz is actually relevant to — those
     * enrolled in the course it's attached to, or with progress on the
     * Darsat lesson it's attached to. Standalone quizzes have no defined
     * audience here, so they're deliberately skipped rather than
     * notifying every student in the system.
     */
    /**
     * The admin's "Kizatangira ryari?" datetime-local input is captured in
     * their browser's local time (this platform runs on Kigali time in
     * practice), but this app's config('app.timezone') is UTC — with no
     * conversion, the naive string gets stored and compared as if it were
     * already UTC, silently shifting every scheduled start time by
     * whatever the Kigali/UTC offset is (currently +2 hours). That made a
     * quiz "start" roughly 2 hours later than the admin actually typed,
     * and — since students never get an artificially early start — this
     * only ever showed up as the countdown widget refusing to flip to the
     * Start button even after the admin's own wall clock said it should
     * have. Explicitly parsing the input as Africa/Kigali time and
     * converting to UTC here is the fix.
     */
    private function parseStartsAt(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d\TH:i', $value, 'Africa/Kigali')->setTimezone('UTC');
    }

    /**
     * Real push notification, sent to every subscribed browser/device —
     * guests included, matching the same broad-announcement philosophy
     * as the in-page popup (QuizAlertController::upcoming()), not the
     * narrower "relevant to this student" audience used for in-app DB
     * notifications above. A guest with no account and no enrollment
     * still has a device that can receive a push.
     */
    private function broadcastPushIfScheduled(Quiz $quiz): void
    {
        if (!$quiz->starts_at || $quiz->starts_at->isPast() || $quiz->status !== 'published') {
            return;
        }

        $whenLocal = $quiz->starts_at->copy()->setTimezone('Africa/Kigali')->format('M j, g:i A');

        app(PushNotificationService::class)->sendToAll(
            'Ikizamini Gishya: ' . $quiz->title,
            'Kizatangira ' . $whenLocal . '. Kanda urebe.',
            route('student.quizzes.history')
        );
    }

    private function notifyRelevantStudentsIfScheduled(Quiz $quiz): void
    {
        if (!$quiz->starts_at || $quiz->starts_at->isPast() || $quiz->status !== 'published') {
            return;
        }

        $students = collect();

        if ($quiz->quizzable_type === CourseLesson::class) {
            $lesson = CourseLesson::find($quiz->quizzable_id);
            if ($lesson) {
                $userIds = \App\Models\CourseEnrollment::where('course_id', $lesson->course_id)->pluck('user_id');
                $students = \App\Models\User::whereIn('id', $userIds)->get();
            }
        } elseif ($quiz->quizzable_type === DarsatTable::class) {
            $userIds = \App\Models\DarsatProgress::where('darsat_id', $quiz->quizzable_id)->pluck('user_id');
            $students = \App\Models\User::whereIn('id', $userIds)->get();
        }

        if ($students->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($students, new \App\Notifications\QuizScheduledNotification($quiz));
        }
    }

    public function destroy($id)
    {
        Quiz::findOrFail($id)->delete();

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyasibwe.');
    }

    /**
     * Question/answer builder for one quiz.
     */
    public function show($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        return view('Users.admin.quiz-builder', compact('quiz'));
    }

    public function storeQuestion(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $request->validate([
            'question'     => 'required|string',
            'type'         => 'required|in:multiple_choice,true_false,short_answer',
            'points'       => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
            'short_answer' => 'required_if:type,short_answer|nullable|string|max:255',
            'answers'      => 'required_if:type,multiple_choice|nullable|array',
            'answers.*'    => 'nullable|string|max:255',
            'correct'      => 'required_if:type,multiple_choice,true_false|nullable',
        ]);

        $nextOrder = ($quiz->questions()->max('order') ?? 0) + 1;

        $question = QuizQuestion::create([
            'quiz_id'      => $quiz->id,
            'question'     => $request->question,
            'type'         => $request->type,
            'short_answer' => $request->type === 'short_answer' ? $request->short_answer : null,
            'points'       => $request->points,
            'order'        => $nextOrder,
            'time_limit_seconds' => $request->time_limit_seconds,
        ]);

        if ($request->type === 'multiple_choice') {
            foreach ($request->input('answers', []) as $index => $text) {
                if (trim((string) $text) === '') {
                    continue;
                }
                QuizAnswer::create([
                    'quiz_question_id' => $question->id,
                    'answer_text'      => $text,
                    'is_correct'       => (string) $request->correct === (string) $index,
                    'order'            => $index,
                ]);
            }
        } elseif ($request->type === 'true_false') {
            QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'True', 'is_correct' => $request->correct === 'true', 'order' => 0]);
            QuizAnswer::create(['quiz_question_id' => $question->id, 'answer_text' => 'False', 'is_correct' => $request->correct === 'false', 'order' => 1]);
        }

        return back()->with('success', 'Ikibazo cyongewe.');
    }

    public function updateQuestion(Request $request, $questionId)
    {
        $question = QuizQuestion::findOrFail($questionId);

        $request->validate([
            'question' => 'required|string',
            'points'   => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
        ]);

        $question->update([
            'question' => $request->question,
            'points'   => $request->points,
            'time_limit_seconds' => $request->time_limit_seconds,
        ]);

        return back()->with('success', 'Ikibazo cyahinduwe.');
    }

    public function destroyQuestion($questionId)
    {
        QuizQuestion::findOrFail($questionId)->delete();

        return back()->with('success', 'Ikibazo cyakuweho.');
    }
}
