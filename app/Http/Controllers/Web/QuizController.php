<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\DarsatTable;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(private QuizService $quizzes)
    {
        $this->middleware('permission:quizzes.view')->only(['index', 'show']);
        $this->middleware('permission:quizzes.create')->only(['store', 'storeQuestion']);
        $this->middleware('permission:quizzes.update')->only(['update', 'updateQuestion']);
        $this->middleware('permission:quizzes.delete')->only(['destroy', 'destroyQuestion']);
    }

    public function index()
    {
        $quizzes = $this->quizzes->paginate(10);
        $courseLessons = CourseLesson::with('course')->orderBy('title')->get();
        $darsatLessons = DarsatTable::published()->orderBy('title')->get();

        return view('Users.admin.quizzes', compact('quizzes', 'courseLessons', 'darsatLessons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'attach_type'         => 'nullable|in:course_lesson,darsat,none',
            'attach_id'           => 'nullable|integer',
            'passing_percentage'  => 'required|integer|min:1|max:100',
            'status'              => 'required|in:draft,published',
            'starts_at'           => 'nullable|date',
            'duration_minutes'    => 'nullable|integer|min:1|max:600',
        ]);

        $this->quizzes->create($data, auth('owner')->id());

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'status'             => 'required|in:draft,published',
            'starts_at'          => 'nullable|date',
            'duration_minutes'   => 'nullable|integer|min:1|max:600',
        ]);

        $this->quizzes->update($quiz, $data, auth('owner')->id());

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyahinduwe.');
    }

    public function destroy($id)
    {
        $this->quizzes->delete(Quiz::findOrFail($id));

        return redirect()->route('owner.quizzes')->with('success', 'Ikizamini cyasibwe.');
    }

    /**
     * Question/answer builder for one quiz.
     */
    public function show($id)
    {
        $quiz = $this->quizzes->findWithQuestions($id);

        return view('Users.admin.quiz-builder', compact('quiz'));
    }

    public function storeQuestion(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $data = $request->validate([
            'question'     => 'required|string',
            'type'         => 'required|in:multiple_choice,true_false,short_answer',
            'points'       => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
            'short_answer' => 'required_if:type,short_answer|nullable|string|max:255',
            'answers'      => 'required_if:type,multiple_choice|nullable|array',
            'answers.*'    => 'nullable|string|max:255',
            'correct'      => 'required_if:type,multiple_choice,true_false|nullable',
        ]);

        $this->quizzes->createQuestion($quiz, $data);

        return back()->with('success', 'Ikibazo cyongewe.');
    }

    public function updateQuestion(Request $request, $questionId)
    {
        $question = QuizQuestion::findOrFail($questionId);

        $data = $request->validate([
            'question' => 'required|string',
            'points'   => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
        ]);

        $this->quizzes->updateQuestion($question, $data);

        return back()->with('success', 'Ikibazo cyahinduwe.');
    }

    public function destroyQuestion($questionId)
    {
        $this->quizzes->deleteQuestion(QuizQuestion::findOrFail($questionId));

        return back()->with('success', 'Ikibazo cyakuweho.');
    }
}
