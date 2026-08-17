<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\LearningStreak;
use App\Models\UserBadge;
use Illuminate\Support\Facades\Auth;

class StudentBadgeController extends Controller
{
    public function index()
    {
        $user = Auth::guard('student')->user();

        $streak = LearningStreak::where('user_id', $user->id)->first();
        $earnedBadgeIds = UserBadge::where('user_id', $user->id)->pluck('badge_id');

        $allBadges = Badge::orderBy('criteria_value')->get();
        $earned = UserBadge::with('badge')->where('user_id', $user->id)->latest('earned_at')->get();

        return view('Users.User.badges', compact('streak', 'allBadges', 'earnedBadgeIds', 'earned'));
    }
}
