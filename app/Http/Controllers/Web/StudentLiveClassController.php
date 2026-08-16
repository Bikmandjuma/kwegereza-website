<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Services\LiveClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLiveClassController extends Controller
{
    public function __construct(private LiveClassService $liveClasses)
    {
    }

    public function show(int $id)
    {
        $class = LiveClass::with('host')->findOrFail($id);
        $student = Auth::guard('student')->user();

        return view('Users.User.live-class', [
            'liveClass' => $class,
            'student' => $student,
        ]);
    }

    /**
     * The owner side already had this (LiveClassService::activeParticipants,
     * reused as-is here); the student side never did. Without it, a
     * student's WebRTC setup has no way to discover who else is already
     * in the room and needs to be called — the same gap that made the
     * shareable-link feature genuinely incomplete without this endpoint.
     */
    public function participants(int $id)
    {
        $class = LiveClass::findOrFail($id);

        return response()->json(['success' => true, 'message' => null, 'data' => $this->liveClasses->activeParticipants($class)]);
    }

    public function join(int $id)
    {
        $class = LiveClass::findOrFail($id);

        if (! $class->isLive()) {
            return response()->json(['success' => false, 'message' => 'Iri somo ntabwo ririmo gukorwa.'], 422);
        }

        $student = Auth::guard('student')->user();
        $participant = $this->liveClasses->join($class, 'student', $student->id);

        return response()->json(['success' => true, 'message' => null, 'data' => ['participant_id' => $participant->id, 'is_muted' => $participant->is_muted]]);
    }

    public function leave(int $id)
    {
        $student = Auth::guard('student')->user();
        $participant = LiveClassParticipant::where('live_class_id', $id)
            ->where('participant_type', 'student')->where('participant_id', $student->id)
            ->whereNull('left_at')->first();

        if ($participant) {
            $this->liveClasses->leave($participant);
        }

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function raiseHand(int $id)
    {
        $student = Auth::guard('student')->user();
        $participant = LiveClassParticipant::where('live_class_id', $id)
            ->where('participant_type', 'student')->where('participant_id', $student->id)
            ->whereNull('left_at')->firstOrFail();

        $this->liveClasses->raiseHand($participant, true);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function lowerHand(int $id)
    {
        $student = Auth::guard('student')->user();
        $participant = LiveClassParticipant::where('live_class_id', $id)
            ->where('participant_type', 'student')->where('participant_id', $student->id)
            ->whereNull('left_at')->firstOrFail();

        $this->liveClasses->raiseHand($participant, false);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function signal(Request $request, int $id)
    {
        $request->validate(['to' => 'required|string', 'signal' => 'required|array']);
        $class = LiveClass::findOrFail($id);
        $student = Auth::guard('student')->user();

        $this->liveClasses->relaySignal($class, 'student:'.$student->id, $request->to, $request->signal);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }
}
