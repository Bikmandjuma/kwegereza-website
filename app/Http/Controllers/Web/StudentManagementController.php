<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\DarsatProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:students.view')->only(['index', 'show']);
        $this->middleware('permission:students.manage')->only(['block', 'unblock']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $students = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('firstname')
            ->paginate(20)
            ->withQueryString();

        return view('Users.admin.student-management', compact('students', 'search'));
    }

    public function show($id)
    {
        $student = User::findOrFail($id);

        $completedDarsat = DarsatProgress::where('user_id', $id)->where('status', 'completed')->count();
        $quizAttempts = QuizAttempt::with('quiz')->where('user_id', $id)->whereNotNull('submitted_at')->latest('submitted_at')->limit(10)->get();
        $certificates = Certificate::where('user_id', $id)->get();
        $badges = UserBadge::with('badge')->where('user_id', $id)->get();

        return view('Users.admin.student-detail', compact('student', 'completedDarsat', 'quizAttempts', 'certificates', 'badges'));
    }

    /**
     * Blocks a student's access immediately — reuses the same
     * deactivated_at mechanism built for admin-approved account deletion
     * requests, which already forces logout on next page load
     * (StudentAuthMiddleware) and blocks future logins
     * (StudentAuthController). This is a direct admin action, not a
     * deletion — nothing about the student's data or history changes.
     */
    public function block($id)
    {
        $student = User::findOrFail($id);
        $student->update(['deactivated_at' => now()]);

        return back()->with('success', $student->firstname . ' yahagaritswe kubona urubuga.');
    }

    public function unblock($id)
    {
        $student = User::findOrFail($id);
        $student->update(['deactivated_at' => null]);

        return back()->with('success', $student->firstname . ' yongeye kubona urubuga.');
    }
}
