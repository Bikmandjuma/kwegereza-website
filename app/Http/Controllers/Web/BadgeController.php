<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gamification.view')->only(['index']);
        $this->middleware('permission:gamification.manage')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $badges = Badge::withCount('userBadges')->orderBy('criteria_value')->get();

        return view('Users.admin.badges', compact('badges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:255',
            'icon'           => 'nullable|string|max:10',
            'criteria_type'  => 'required|in:streak_days,darsat_completed,courses_completed,quizzes_passed',
            'criteria_value' => 'required|integer|min:1',
        ]);

        Badge::create([
            'name'           => $request->name,
            'slug'           => Str::slug($request->name),
            'description'    => $request->description,
            'icon'           => $request->icon ?: '🏅',
            'criteria_type'  => $request->criteria_type,
            'criteria_value' => $request->criteria_value,
        ]);

        return back()->with('success', 'Ikimenyetso cyashyizweho.');
    }

    /**
     * Referenced by this controller's own constructor
     * (->middleware('permission:gamification.manage')->only([...,
     * 'update', ...])) since it was first written, but never actually
     * implemented, and no route ever pointed at it — so unlike similar
     * missing-method bugs found in earlier phases, this one was never
     * live/fatal-erroring, just a genuine missing feature: there was no
     * way to edit a badge's name or criteria after creating it.
     */
    public function update(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:255',
            'icon'           => 'nullable|string|max:10',
            'criteria_type'  => 'required|in:streak_days,darsat_completed,courses_completed,quizzes_passed',
            'criteria_value' => 'required|integer|min:1',
        ]);

        $badge->update([
            'name'           => $request->name,
            'slug'           => Str::slug($request->name),
            'description'    => $request->description,
            'icon'           => $request->icon ?: $badge->icon,
            'criteria_type'  => $request->criteria_type,
            'criteria_value' => $request->criteria_value,
        ]);

        return back()->with('success', 'Ikimenyetso cyahinduwe.');
    }

    public function destroy($id)
    {
        Badge::findOrFail($id)->delete();

        return back()->with('success', 'Ikimenyetso cyasibwe.');
    }
}
