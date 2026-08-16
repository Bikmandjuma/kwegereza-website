<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DarsatProgress;
use App\Models\DarsatTable;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarsatProgressController extends Controller
{
    public function __construct(private GamificationService $gamification)
    {
    }
    /**
     * Called whenever a logged-in student plays a lesson. Creates the
     * progress row on first play, otherwise just bumps the counters.
     */
    public function trackPlay(Request $request, $darsatId)
    {
        $darsat = DarsatTable::findOrFail($darsatId);
        $user = Auth::guard('student')->user();

        $progress = DarsatProgress::firstOrNew([
            'user_id'   => $user->id,
            'darsat_id' => $darsat->id,
        ]);

        if (!$progress->exists) {
            $progress->status = 'in_progress';
        }

        $progress->times_played = ($progress->times_played ?? 0) + 1;
        $progress->last_played_at = now();
        $progress->save();

        return response()->json(['ok' => true, 'status' => $progress->status]);
    }

    /**
     * Marks a lesson as fully completed. Called by the "Nyandika ko
     * warangije" button — deliberately explicit/manual for now rather than
     * inferred from audio playback percentage, since the existing player's
     * internal JS wasn't something I wanted to reverse-engineer and risk
     * breaking for an implicit "played to 90%" heuristic.
     */
    public function markCompleted(Request $request, $darsatId)
    {
        $darsat = DarsatTable::findOrFail($darsatId);
        $user = Auth::guard('student')->user();

        $progress = DarsatProgress::updateOrCreate(
            ['user_id' => $user->id, 'darsat_id' => $darsat->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $result = $this->gamification->recordActivityAndCheckBadges($user);

        return response()->json([
            'ok'          => true,
            'status'      => $progress->status,
            'newBadges'   => $result['newlyEarned']->map(fn($b) => ['name' => $b->name, 'icon' => $b->icon])->values(),
            'streak'      => $result['streak']->current_streak,
        ]);
    }

    /**
     * "My Progress" page.
     */
    public function myProgress()
    {
        $user = Auth::guard('student')->user();

        $progress = $user->darsatProgress()
            ->with(['darsat.teacher'])
            ->latest('updated_at')
            ->get();

        $completedCount = $progress->where('status', 'completed')->count();
        $inProgressCount = $progress->where('status', 'in_progress')->count();

        return view('Users.User.progress', compact('progress', 'completedCount', 'inProgressCount'));
    }
}
