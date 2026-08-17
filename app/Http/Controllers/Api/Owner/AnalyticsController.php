<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analytics)
    {
    }

    public function overview(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => [
                'most_played_darsat' => $this->analytics->mostPlayedDarsat(),
                'most_downloaded_books' => $this->analytics->mostDownloadedBooks(),
                'most_viewed_books' => $this->analytics->mostViewedBooks(),
                'exam_participation' => $this->analytics->examParticipation(),
                'daily_active_trend' => $this->analytics->dailyActiveTrend((int) $request->integer('days', 7)),
                'most_active_students' => $this->analytics->mostActiveStudents(),
            ],
        ]);
    }
}
