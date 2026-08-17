<?php

namespace App\Services;

use App\Models\Amatangazo;
use App\Models\DailyActiveSnapshot;
use App\Models\DarsatTable;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Builds real time-series data for the admin dashboard's 4 charts:
 * students joined, Darsat added, Amatangazo published, and online users —
 * each independently switchable between day/week/month/year.
 *
 * Honest limitation on the "online users" series: `users.last_active_at`
 * only ever stores a user's MOST RECENT activity — it's overwritten every
 * time, so there is no way to reconstruct how many were online on a day
 * that has already passed from that column alone. To give this metric a
 * real (not fabricated) history, `recordTodaysSnapshot()` opportunistically
 * upserts today's peak online count every time the dashboard loads. This
 * means the chart is accurate and grows for real from the day this
 * feature ships onward, but has no data for days before that. A proper
 * fixed-interval snapshot (every 5 minutes, say) would be more complete,
 * but needs a working queue/scheduler — this app's QUEUE_CONNECTION is
 * `sync` with no scheduler confirmed running, so this lazier approach
 * is the honest, working alternative rather than a cron job that might
 * silently never fire.
 */
class DashboardChartService
{
    public function recordTodaysSnapshot(int $currentOnlineCount): void
    {
        $today = Carbon::today()->toDateString();

        $snapshot = DailyActiveSnapshot::firstOrCreate(
            ['date' => $today],
            ['peak_online_count' => $currentOnlineCount]
        );

        if ($currentOnlineCount > $snapshot->peak_online_count) {
            $snapshot->update(['peak_online_count' => $currentOnlineCount]);
        }
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function seriesFor(string $metric, string $period): array
    {
        $period = in_array($period, ['day', 'week', 'month', 'year'], true) ? $period : 'day';

        return match ($metric) {
            'students'    => $this->countSeries(User::query(), $period),
            'darsat'      => $this->countSeries(DarsatTable::query(), $period),
            'amatangazo'  => $this->countSeries(Amatangazo::query(), $period),
            'online_users'=> $this->onlineSeries($period),
            default       => ['labels' => [], 'data' => []],
        };
    }

    private function countSeries($query, string $period): array
    {
        [$points, $formatLabel, $startOf, $endOf] = $this->pointsFor($period);

        $labels = [];
        $data = [];

        foreach ($points as $point) {
            $rangeStart = $startOf($point);
            $rangeEnd = $endOf($point);

            $count = (clone $query)->whereBetween('created_at', [$rangeStart, $rangeEnd])->count();

            $labels[] = $formatLabel($point);
            $data[] = $count;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function onlineSeries(string $period): array
    {
        [$points, $formatLabel, $startOf, $endOf] = $this->pointsFor($period);

        $labels = [];
        $data = [];

        foreach ($points as $point) {
            $rangeStart = $startOf($point);
            $rangeEnd = $endOf($point);

            // Peak-of-peaks within this range (max, not sum) — so a
            // month doesn't nonsensically show "300 online" just
            // because 30 days each peaked at 10.
            $peak = DailyActiveSnapshot::whereBetween('date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
                ->max('peak_online_count');

            $labels[] = $formatLabel($point);
            $data[] = (int) ($peak ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Returns [points, labelFormatter, startOfRangeFn, endOfRangeFn] for
     * the requested period — the shared scaffolding every metric's
     * series is built from.
     */
    private function pointsFor(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'week' => [
                collect(range(11, 0))->map(fn($i) => $now->copy()->subWeeks($i)->startOfWeek()),
                fn($p) => $p->format('M j'),
                fn($p) => $p->copy()->startOfWeek(),
                fn($p) => $p->copy()->endOfWeek(),
            ],
            'month' => [
                collect(range(11, 0))->map(fn($i) => $now->copy()->subMonths($i)->startOfMonth()),
                fn($p) => $p->format('M Y'),
                fn($p) => $p->copy()->startOfMonth(),
                fn($p) => $p->copy()->endOfMonth(),
            ],
            'year' => [
                collect(range(4, 0))->map(fn($i) => $now->copy()->subYears($i)->startOfYear()),
                fn($p) => $p->format('Y'),
                fn($p) => $p->copy()->startOfYear(),
                fn($p) => $p->copy()->endOfYear(),
            ],
            default => [ // 'day'
                collect(range(13, 0))->map(fn($i) => $now->copy()->subDays($i)->startOfDay()),
                fn($p) => $p->format('M j'),
                fn($p) => $p->copy()->startOfDay(),
                fn($p) => $p->copy()->endOfDay(),
            ],
        };
    }
}
