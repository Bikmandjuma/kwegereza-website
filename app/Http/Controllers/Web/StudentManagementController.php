<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function __construct(private StudentService $students)
    {
        $this->middleware('permission:students.view')->only(['index', 'show']);
        $this->middleware('permission:students.manage')->only(['block', 'unblock']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $students = $this->students->paginate(20, $search)->withQueryString();

        return view('Users.admin.student-management', compact('students', 'search'));
    }

    public function show($id)
    {
        $detail = $this->students->detail((int) $id);

        return view('Users.admin.student-detail', [
            'student' => $detail['student'],
            'completedDarsat' => $detail['completed_darsat_count'],
            'quizAttempts' => $detail['recent_quiz_attempts'],
            'certificates' => $detail['certificates'],
            'badges' => $detail['badges'],
        ]);
    }

    public function block($id)
    {
        $student = $this->students->block(User::findOrFail($id));

        return back()->with('success', $student->firstname . ' yahagaritswe kubona urubuga.');
    }

    public function unblock($id)
    {
        $student = $this->students->unblock(User::findOrFail($id));

        return back()->with('success', $student->firstname . ' yongeye kubona urubuga.');
    }
}
