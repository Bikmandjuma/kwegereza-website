<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:feature_flags.manage');
    }

    public function index()
    {
        $flags = FeatureFlag::orderBy('label')->get();

        return view('Users.admin.feature-flags', compact('flags'));
    }

    public function toggle($id)
    {
        $flag = FeatureFlag::findOrFail($id);
        $flag->update(['is_enabled' => !$flag->is_enabled]);

        return back()->with('success', $flag->label . ' ' . ($flag->is_enabled ? 'yashyizweho gukora.' : 'yahagaritswe.'));
    }
}
