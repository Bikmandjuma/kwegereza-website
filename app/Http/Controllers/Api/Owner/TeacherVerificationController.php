<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;

class TeacherVerificationController extends Controller
{
    public function index()
    {
        $teachers = Owner::whereIn('title', ['sheikh', 'ustadh'])
            ->orderByDesc('is_verified')
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'lastname', 'title', 'bio', 'credentials', 'is_verified', 'verified_at']);

        return response()->json(['success' => true, 'message' => null, 'data' => $teachers]);
    }

    public function verify(Request $request, int $id)
    {
        $teacher = Owner::findOrFail($id);
        $teacher->update(['is_verified' => true, 'verified_at' => now(), 'verified_by' => $request->user()->id]);

        return response()->json(['success' => true, 'message' => $teacher->firstname.' yemejwe (verified).', 'data' => null]);
    }

    public function unverify(int $id)
    {
        $teacher = Owner::findOrFail($id);
        $teacher->update(['is_verified' => false, 'verified_at' => null, 'verified_by' => null]);

        return response()->json(['success' => true, 'message' => $teacher->firstname.' ntakiri verified.', 'data' => null]);
    }

    public function updateProfile(Request $request, int $id)
    {
        $data = $request->validate([
            'bio' => 'nullable|string|max:2000',
            'credentials' => 'nullable|string|max:2000',
        ]);

        Owner::findOrFail($id)->update($data);

        return response()->json(['success' => true, 'message' => 'Umwirondoro wahinduwe.', 'data' => null]);
    }
}
