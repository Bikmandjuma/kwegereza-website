<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private \App\Services\CertificateService $certificates)
    {
        $this->middleware('permission:certificates.view')->only(['index']);
        $this->middleware('permission:certificates.issue')->only(['store']);
    }

    public function index()
    {
        $certificates = $this->certificates->paginate(15);
        $users = User::orderBy('firstname')->get();
        $courses = Course::orderBy('title')->get();

        return view('Users.admin.certificates', compact('certificates', 'users', 'courses'));
    }

    /**
     * Manual issuance — for cases outside the automatic
     * course-completion trigger (e.g. an offline/in-person program).
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'title'     => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $this->certificates->issue(
            User::findOrFail($request->user_id),
            $request->title,
            $request->course_id ?: null,
            auth('owner')->id()
        );

        return back()->with('success', 'Icyemezo cyatanzwe.');
    }
}
