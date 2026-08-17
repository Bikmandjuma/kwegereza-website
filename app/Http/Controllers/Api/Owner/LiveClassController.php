<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Services\LiveClassService;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    public function __construct(private LiveClassService $liveClasses)
    {
    }

    public function index()
    {
        $classes = LiveClass::with('host')->latest()->paginate(10);

        return response()->json([
            'success' => true, 'message' => null,
            'data' => LiveClassResource::collection($classes->items()),
            'meta' => ['current_page' => $classes->currentPage(), 'last_page' => $classes->lastPage(), 'total' => $classes->total()],
        ]);
    }

    /**
     * Was missing entirely — the frontend had no way to check "am I
     * actually the host of this specific class" before rendering host-only
     * controls, so any leader with the broad live_class.manage permission
     * (needed just to see the list at all) saw Start/End/mute-all/remove
     * buttons on every class, not just their own, and got a raw 403 from
     * authorizeHost() the moment they clicked one that wasn't theirs.
     */
    public function show(int $id)
    {
        $class = LiveClass::with('host')->findOrFail($id);

        return response()->json(['success' => true, 'message' => null, 'data' => new LiveClassResource($class)]);
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'scheduled_at' => 'nullable|date']);

        $class = $this->liveClasses->create(
            $request->title,
            $request->user(),
            $request->scheduled_at ? \Illuminate\Support\Carbon::parse($request->scheduled_at) : null
        );

        return response()->json(['success' => true, 'message' => 'Isomo rya live ryashyizweho.', 'data' => new LiveClassResource($class)], 201);
    }

    public function start(int $id)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);

        $class = $this->liveClasses->start($class);
        $this->liveClasses->join($class, 'owner', $class->host_id);

        return response()->json(['success' => true, 'message' => 'Isomo ritangiye.', 'data' => new LiveClassResource($class)]);
    }

    public function end(int $id)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);

        $class = $this->liveClasses->end($class);

        return response()->json(['success' => true, 'message' => 'Isomo ryarangiye.', 'data' => new LiveClassResource($class)]);
    }

    public function participants(int $id)
    {
        $class = LiveClass::findOrFail($id);

        return response()->json(['success' => true, 'message' => null, 'data' => $this->liveClasses->activeParticipants($class)]);
    }

    public function approveHand(Request $request, int $id, int $participantId)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);
        $participant = LiveClassParticipant::where('live_class_id', $id)->findOrFail($participantId);

        $this->liveClasses->approveHand($participant);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function rejectHand(Request $request, int $id, int $participantId)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);
        $participant = LiveClassParticipant::where('live_class_id', $id)->findOrFail($participantId);

        $this->liveClasses->rejectHand($participant);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function muteParticipant(int $id, int $participantId)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);
        $participant = LiveClassParticipant::where('live_class_id', $id)->findOrFail($participantId);

        $this->liveClasses->muteParticipant($participant);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function muteEveryone(int $id)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);

        $this->liveClasses->muteEveryone($class);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function removeParticipant(int $id, int $participantId)
    {
        $class = LiveClass::findOrFail($id);
        $this->authorizeHost($class, $id);
        $participant = LiveClassParticipant::where('live_class_id', $id)->findOrFail($participantId);

        $this->liveClasses->removeParticipant($participant);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    public function signal(Request $request, int $id)
    {
        $request->validate(['to' => 'required|string', 'signal' => 'required|array']);
        $class = LiveClass::findOrFail($id);
        $from = 'owner:'.$request->user()->id;

        $this->liveClasses->relaySignal($class, $from, $request->to, $request->signal);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    /**
     * live_class.manage lets a leader/teacher run ANY class, but only the
     * class's own host (or someone with the broader live_class.moderate
     * permission — e.g. a moderator stepping in) can control THIS
     * specific class. Object-level, not just slug-level, matching the
     * open question flagged back in Phase 1 about leaders editing content
     * that isn't theirs.
     */
    private function authorizeHost(LiveClass $class, int $id): void
    {
        $user = request()->user();
        if ($class->host_id !== $user->id && ! $user->hasPermission('live_class.moderate')) {
            abort(403, 'Ntabwo uri umuyobozi w\'iri somo.');
        }
    }
}
