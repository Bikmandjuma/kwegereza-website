<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizAlertController extends Controller
{
    /**
     * The single soonest upcoming quiz across the whole platform, for the
     * site-wide auto-popup shown to guests and students alike. Genuinely
     * public — no permission gate, no guard check — since a guest with no
     * account at all still needs to see this.
     *
     * Deliberately not audience-restricted the way the dashboard's
     * "relevant quizzes" logic is (course-enrollment / Darsat-progress
     * based) — a guest has no enrollment to check against, and treating
     * this as a general "here's something coming up" announcement is what
     * was actually asked for, not a personalized feed.
     */
    public function upcoming(Request $request)
    {
        $quiz = Quiz::published()
            ->whereNotNull('starts_at')
            ->where('starts_at', '>', now())
            ->where('starts_at', '<=', now()->addDays(7))
            ->orderBy('starts_at')
            ->first();

        if (!$quiz) {
            return response()->json(['quiz' => null]);
        }

        return response()->json([
            'quiz' => [
                'id'              => $quiz->id,
                'title'           => $quiz->title,
                'starts_at'       => $quiz->starts_at->toIso8601String(),
                'starts_at_local' => $quiz->starts_at->copy()->setTimezone('Africa/Kigali')->format('l, M j, Y g:i A'),
            ],
        ]);
    }
}
