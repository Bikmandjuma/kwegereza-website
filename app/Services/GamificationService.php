<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\CourseEnrollment;
use App\Models\DarsatProgress;
use App\Models\LearningStreak;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Deliberately simple, deliberately centralized. Every place in the app
 * that represents "the student did some learning today" calls
 * recordActivityAndCheckBadges() once — it updates the streak and checks
 * every badge's criteria in one pass. Adding a new badge type later means
 * adding one case to meetsCriteria(), not touching every controller that
 * might award it.
 */
class GamificationService
{
    /**
     * @return array{streak: LearningStreak, newlyEarned: Collection<int, Badge>}
     */
    public function recordActivityAndCheckBadges(User $user): array
    {
        $streak = $this->updateStreak($user);
        $newlyEarned = $this->checkAndAwardBadges($user, $streak);

        return ['streak' => $streak, 'newlyEarned' => $newlyEarned];
    }

    private function updateStreak(User $user): LearningStreak
    {
        $streak = LearningStreak::firstOrCreate(['user_id' => $user->id]);

        $today = Carbon::today();
        $last = $streak->last_activity_date;

        if ($last && $last->isSameDay($today)) {
            // Already recorded activity today — no change.
        } elseif ($last && $last->isSameDay($today->copy()->subDay())) {
            $streak->current_streak += 1;
        } else {
            $streak->current_streak = 1;
        }

        $streak->longest_streak = max($streak->longest_streak, $streak->current_streak);
        $streak->last_activity_date = $today;
        $streak->save();

        return $streak;
    }

    private function checkAndAwardBadges(User $user, LearningStreak $streak): Collection
    {
        $earnedBadgeIds = UserBadge::where('user_id', $user->id)->pluck('badge_id');

        $candidates = Badge::whereNotIn('id', $earnedBadgeIds)->get();
        $newlyEarned = collect();

        foreach ($candidates as $badge) {
            if ($this->meetsCriteria($user, $badge, $streak)) {
                UserBadge::create([
                    'user_id'   => $user->id,
                    'badge_id'  => $badge->id,
                    'earned_at' => now(),
                ]);
                $newlyEarned->push($badge);
            }
        }

        return $newlyEarned;
    }

    private function meetsCriteria(User $user, Badge $badge, LearningStreak $streak): bool
    {
        return match ($badge->criteria_type) {
            'streak_days'       => $streak->current_streak >= $badge->criteria_value,
            'darsat_completed'  => DarsatProgress::where('user_id', $user->id)->where('status', 'completed')->count() >= $badge->criteria_value,
            'courses_completed' => CourseEnrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count() >= $badge->criteria_value,
            'quizzes_passed'    => QuizAttempt::where('user_id', $user->id)->where('passed', true)->distinct('quiz_id')->count('quiz_id') >= $badge->criteria_value,
            default             => false,
        };
    }
}
