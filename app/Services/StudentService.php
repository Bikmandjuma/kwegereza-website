<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\DarsatProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;

/**
 * Owner/Leader-side student management — extracted from
 * Web\StudentManagementController, same pattern as CourseService/DarsatService.
 *
 * Deliberately does NOT cover the student's own dashboard/course-taking
 * experience — that runs on the separate 'student' session guard with no
 * RBAC (students don't hold roles/permissions), so it isn't part of this
 * owner-side API pattern. See the Phase 5 report for why that's a distinct
 * piece of work.
 */
class StudentService
{
    public function paginate(int $perPage = 20, ?string $search = null)
    {
        return User::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('firstname')
            ->paginate($perPage);
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function detail(int $id): array
    {
        return [
            'student' => $this->find($id),
            'completed_darsat_count' => DarsatProgress::where('user_id', $id)->where('status', 'completed')->count(),
            'recent_quiz_attempts' => QuizAttempt::with('quiz')
                ->where('user_id', $id)->whereNotNull('submitted_at')
                ->latest('submitted_at')->limit(10)->get(),
            'certificates' => Certificate::where('user_id', $id)->get(),
            'badges' => UserBadge::with('badge')->where('user_id', $id)->get(),
        ];
    }

    public function block(User $student): User
    {
        $student->update(['deactivated_at' => now()]);

        return $student;
    }

    public function unblock(User $student): User
    {
        $student->update(['deactivated_at' => null]);

        return $student;
    }

    /**
     * Same 5-minute threshold as UpdateLastActive's own transition check —
     * kept in one place (here) so the "who's online right now" list and
     * the middleware that fires the transition event agree on what
     * "online" means, rather than two independently-chosen thresholds
     * silently drifting apart.
     */
    public const ONLINE_THRESHOLD_MINUTES = 5;

    public function onlineStudents()
    {
        return User::where('last_active_at', '>=', now()->subMinutes(self::ONLINE_THRESHOLD_MINUTES))
            ->orderByDesc('last_active_at')
            ->get(['id', 'firstname', 'lastname', 'last_active_at']);
    }

    public function onlineCount(): int
    {
        return User::where('last_active_at', '>=', now()->subMinutes(self::ONLINE_THRESHOLD_MINUTES))->count();
    }
}
