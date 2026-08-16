<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Owner\StoreQuizQuestionRequest;
use App\Http\Requests\Api\Owner\StoreQuizRequest;
use App\Http\Requests\Api\Owner\UpdateQuizQuestionRequest;
use App\Http\Requests\Api\Owner\UpdateQuizRequest;
use App\Http\Resources\QuizQuestionResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(private QuizService $quizzes)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->quizzes->paginate(
            perPage: (int) $request->integer('per_page', 10),
            search: $request->string('search')->value() ?: null,
            status: $request->string('status')->value() ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => QuizResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => new QuizResource($this->quizzes->findWithQuestions($id)),
        ]);
    }

    public function store(StoreQuizRequest $request)
    {
        $quiz = $this->quizzes->create($request->validated(), $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Ikizamini cyashyizweho.',
            'data' => new QuizResource($quiz),
        ], 201);
    }

    public function update(UpdateQuizRequest $request, int $id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->quizzes->update($quiz, $request->validated(), $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Ikizamini cyahinduwe.',
            'data' => new QuizResource($quiz->fresh()),
        ]);
    }

    public function destroy(int $id)
    {
        $this->quizzes->delete(Quiz::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Ikizamini cyasibwe.', 'data' => null]);
    }

    public function storeQuestion(StoreQuizQuestionRequest $request, int $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = $this->quizzes->createQuestion($quiz, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ikibazo cyongewe.',
            'data' => new QuizQuestionResource($question->load('answers')),
        ], 201);
    }

    public function updateQuestion(UpdateQuizQuestionRequest $request, int $questionId)
    {
        $question = QuizQuestion::findOrFail($questionId);
        $this->quizzes->updateQuestion($question, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ikibazo cyahinduwe.',
            'data' => new QuizQuestionResource($question->fresh('answers')),
        ]);
    }

    public function destroyQuestion(int $questionId)
    {
        $this->quizzes->deleteQuestion(QuizQuestion::findOrFail($questionId));

        return response()->json(['success' => true, 'message' => 'Ikibazo cyakuweho.', 'data' => null]);
    }
}
