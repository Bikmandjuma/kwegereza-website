<?php

namespace App\Services;

use App\Models\BookDownload;
use App\Models\DailyActiveSnapshot;
use App\Models\DarsatProgress;
use App\Models\QuizAttempt;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Deliberately built as rollups over data ALREADY being tracked
 * (DarsatProgress.times_played, BookDownload rows from the Books phase,
 * QuizAttempt) rather than a new fine-grained event log — matching spec
 * section 23's own instruction ("do not write a database row every
 * second") and avoiding duplicating tracking that already exists and
 * works. The gap this phase closes is that nothing aggregated any of it
 * into the actual dashboards the spec describes.
 */
class AnalyticsService
{
    public function mostPlayedDarsat(int $limit = 10)
    {
        return DarsatProgress::selectRaw('darsat_id, SUM(times_played) as total_plays, COUNT(DISTINCT user_id) as unique_listeners')
            ->groupBy('darsat_id')
            ->orderByDesc('total_plays')
            ->with('darsat:id,title,type')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'darsat_id' => $row->darsat_id,
                'title' => $row->darsat?->title,
                'type' => $row->darsat?->type,
                'total_plays' => (int) $row->total_plays,
                'unique_listeners' => (int) $row->unique_listeners,
            ]);
    }

    public function mostDownloadedBooks(int $limit = 10)
    {
        return BookDownload::selectRaw('book_id, COUNT(*) as total_downloads, COUNT(DISTINCT user_id) as unique_downloaders')
            ->groupBy('book_id')
            ->orderByDesc('total_downloads')
            ->with('book:id,title,author')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'book_id' => $row->book_id,
                'title' => $row->book?->title,
                'author' => $row->book?->author,
                'total_downloads' => (int) $row->total_downloads,
                'unique_downloaders' => (int) $row->unique_downloaders,
            ]);
    }

    /**
     * Separate from downloads — a "view" (opening the reader) is a much
     * weaker signal of real interest than an actual download, so this is
     * its own ranking rather than folded into the same list. Reads the
     * `views` counter that was flagged as unused in an earlier migration
     * and only actually started incrementing once the reader UI was
     * wired to call it (see the ibitabo.blade.php fix).
     */
    public function mostViewedBooks(int $limit = 10)
    {
        return Book::where('views', '>', 0)
            ->orderByDesc('views')
            ->limit($limit)
            ->get(['id', 'title', 'author', 'views'])
            ->map(fn ($book) => [
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'views' => $book->views,
            ]);
    }

    public function examParticipation()
    {
        $submitted = QuizAttempt::whereNotNull('submitted_at');

        $total = (clone $submitted)->count();
        $passed = (clone $submitted)->where('passed', true)->count();
        $avgPercentage = (clone $submitted)->avg('percentage');
        $inProgress = QuizAttempt::whereNull('submitted_at')->count();

        return [
            'total_attempts' => $total,
            'passed' => $passed,
            'failed' => $total - $passed,
            'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
            'average_percentage' => $avgPercentage !== null ? round($avgPercentage, 1) : null,
            'in_progress' => $inProgress,
        ];
    }

    /**
     * Reuses the exact same peak_online_count table DashboardChartService
     * already writes to (Phase 0-era code, predating this phase) — one
     * source of truth for "how many students were online," not a second,
     * competing tracking mechanism.
     */
    public function dailyActiveTrend(int $days = 7)
    {
        return DailyActiveSnapshot::where('date', '>=', Carbon::today()->subDays($days - 1))
            ->orderBy('date')
            ->get(['date', 'peak_online_count']);
    }

    public function mostActiveStudents(int $limit = 10)
    {
        return User::whereNotNull('last_active_at')
            ->withCount(['darsatProgress as completed_lessons_count' => fn ($q) => $q->where('status', 'completed')])
            ->orderByDesc('last_active_at')
            ->limit($limit)
            ->get(['id', 'firstname', 'lastname', 'last_active_at']);
    }
}
