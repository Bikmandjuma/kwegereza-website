<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::withCount('userBadges')->orderBy('criteria_value')->get();

        return response()->json(['success' => true, 'message' => null, 'data' => $badges]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:255',
            'icon'           => 'nullable|string|max:10',
            'criteria_type'  => 'required|in:streak_days,darsat_completed,courses_completed,quizzes_passed',
            'criteria_value' => 'required|integer|min:1',
        ]);

        $badge = Badge::create([
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'description'    => $data['description'] ?? null,
            'icon'           => $data['icon'] ?? '🏅',
            'criteria_type'  => $data['criteria_type'],
            'criteria_value' => $data['criteria_value'],
        ]);

        return response()->json(['success' => true, 'message' => 'Ikimenyetso cyashyizweho.', 'data' => $badge], 201);
    }

    public function update(Request $request, int $id)
    {
        $badge = Badge::findOrFail($id);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string|max:255',
            'icon'           => 'nullable|string|max:10',
            'criteria_type'  => 'required|in:streak_days,darsat_completed,courses_completed,quizzes_passed',
            'criteria_value' => 'required|integer|min:1',
        ]);

        $badge->update([
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'description'    => $data['description'] ?? null,
            'icon'           => $data['icon'] ?? $badge->icon,
            'criteria_type'  => $data['criteria_type'],
            'criteria_value' => $data['criteria_value'],
        ]);

        return response()->json(['success' => true, 'message' => 'Ikimenyetso cyahinduwe.', 'data' => $badge->fresh()]);
    }

    public function destroy(int $id)
    {
        Badge::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Ikimenyetso cyasibwe.', 'data' => null]);
    }
}
