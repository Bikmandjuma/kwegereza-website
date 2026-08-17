<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:teacher_verification.manage');
    }

    public function index()
    {
        $teachers = Owner::whereIn('title', ['sheikh', 'ustadh'])
            ->orderByDesc('is_verified')
            ->orderBy('firstname')
            ->get();

        return view('Users.admin.teacher-verification', compact('teachers'));
    }

    public function verify($id)
    {
        $teacher = Owner::findOrFail($id);

        $teacher->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::guard('owner')->id(),
        ]);

        return back()->with('success', $teacher->firstname . ' yemejwe (verified).');
    }

    public function unverify($id)
    {
        $teacher = Owner::findOrFail($id);

        $teacher->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return back()->with('success', $teacher->firstname . ' ntakiri verified.');
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'bio'         => 'nullable|string|max:2000',
            'credentials' => 'nullable|string|max:2000',
        ]);

        Owner::findOrFail($id)->update([
            'bio'         => $request->bio,
            'credentials' => $request->credentials,
        ]);

        return back()->with('success', 'Umwirondoro wahinduwe.');
    }
}
