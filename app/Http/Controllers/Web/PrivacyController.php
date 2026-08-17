<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Models\Certificate;
use App\Models\Comment;
use App\Models\CourseEnrollment;
use App\Models\DarsatProgress;
use App\Models\EventRegistration;
use App\Models\Favorite;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PrivacyController extends Controller
{
    public function settings()
    {
        $user = Auth::guard('student')->user();
        $pendingDeletion = AccountDeletionRequest::where('user_id', $user->id)->where('status', 'pending')->exists();

        return view('Users.User.privacy-settings', compact('user', 'pendingDeletion'));
    }

    public function updateVisibility(Request $request)
    {
        $user = Auth::guard('student')->user();

        $user->update(['profile_visible' => $request->boolean('profile_visible')]);

        return back()->with('success', 'Igenamiterere ryahinduwe.');
    }

    /**
     * Right-to-access style data export — everything this platform knows
     * about the student, as a single downloadable JSON file.
     */
    public function exportData()
    {
        $user = Auth::guard('student')->user();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'profile' => [
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'darsat_progress'    => DarsatProgress::where('user_id', $user->id)->get(['darsat_id', 'status', 'completed_at']),
            'course_enrollments' => CourseEnrollment::where('user_id', $user->id)->get(['course_id', 'enrolled_at', 'completed_at']),
            'certificates'       => Certificate::where('user_id', $user->id)->get(['certificate_number', 'title', 'issued_at']),
            'badges'             => UserBadge::where('user_id', $user->id)->with('badge:id,name')->get()->pluck('badge.name'),
            'event_registrations'=> EventRegistration::where('user_id', $user->id)->get(['event_id', 'registered_at']),
            'comments'           => Comment::where('user_id', $user->id)->get(['content', 'created_at']),
            'favorites'          => Favorite::where('user_id', $user->id)->get(['favoritable_type', 'favoritable_id', 'created_at']),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return Response::make($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="kiu-data-export-' . $user->id . '.json"',
        ]);
    }

    public function requestDeletion(Request $request)
    {
        $user = Auth::guard('student')->user();

        if (AccountDeletionRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->with('error', 'Usanzwe ufite icyifuzo gitegereje.');
        }

        $request->validate(['reason' => 'nullable|string|max:1000']);

        AccountDeletionRequest::create([
            'user_id' => $user->id,
            'reason'  => $request->reason,
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Icyifuzo cyoherejwe. Ubuyobozi buzagisuzuma.');
    }
}
