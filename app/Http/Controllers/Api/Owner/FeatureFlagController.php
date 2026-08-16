<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;

class FeatureFlagController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => FeatureFlag::orderBy('label')->get(['id', 'key', 'label', 'description', 'is_enabled']),
        ]);
    }

    public function toggle(int $id)
    {
        $flag = FeatureFlag::findOrFail($id);
        $flag->update(['is_enabled' => ! $flag->is_enabled]);

        return response()->json([
            'success' => true,
            'message' => $flag->label.' '.($flag->is_enabled ? 'yashyizweho gukora.' : 'yahagaritswe.'),
            'data' => ['id' => $flag->id, 'is_enabled' => $flag->is_enabled],
        ]);
    }
}
