<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupMessageResource;
use App\Models\GroupMessage;
use App\Services\GroupChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Student's own group chat (male/female student group) — a student is
 * placed into exactly one group automatically based on their profile's
 * gender, not a group they choose or that an admin assigns. Runs on the
 * existing session-based 'student' guard, matching the rest of the
 * student-facing app rather than the Sanctum-based owner API pattern.
 */
class StudentGroupChatController extends Controller
{
    public function __construct(private GroupChatService $groupChat)
    {
    }

    public function page()
    {
        $student = Auth::guard('student')->user();
        $group = $this->groupChat->studentGroupFor($student);

        return view('Users.User.group-chat', ['group' => $group]);
    }

    public function messages()
    {
        $student = Auth::guard('student')->user();
        $group = $this->groupChat->studentGroupFor($student);

        if (! $group) {
            return response()->json(['success' => false, 'message' => 'Nta tsinda rifite.'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'group' => $group,
            'data' => GroupMessageResource::collection($this->groupChat->messagesFor($group)),
            'pinned' => GroupMessageResource::collection($this->groupChat->pinnedFor($group)),
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer',
        ]);

        $student = Auth::guard('student')->user();
        $group = $this->groupChat->studentGroupFor($student);

        if (! $group) {
            return response()->json(['success' => false, 'message' => 'Nta tsinda rifite.'], 422);
        }

        try {
            $message = $this->groupChat->postAsStudent($group, $student, $request->message, $request->parent_id);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json(['success' => true, 'message' => null, 'data' => new GroupMessageResource($message)], 201);
    }

    public function react(Request $request, GroupMessage $message)
    {
        $request->validate(['emoji' => 'required|string|max:16']);
        $student = Auth::guard('student')->user();

        $this->assertOwnGroup($student, $message);

        $type = $this->groupChat->react($message, 'student', $student->id, $request->emoji);

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ['type' => $type, 'reactions' => $this->groupChat->reactionSummary($message->fresh())],
        ]);
    }

    public function destroy(GroupMessage $message)
    {
        $student = Auth::guard('student')->user();
        $this->assertOwnGroup($student, $message);

        try {
            $this->groupChat->delete($message, 'student', $student->id, false);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json(['success' => true, 'message' => 'Ubutumwa bwasibwe.']);
    }

    public function report(Request $request, GroupMessage $message)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $student = Auth::guard('student')->user();

        $this->assertOwnGroup($student, $message);

        $this->groupChat->report($message, 'student', $student->id, $request->reason);

        return response()->json(['success' => true, 'message' => 'Raporo yatanzwe.']);
    }

    /** A student can only ever act on messages inside their own assigned group — never someone else's group by guessing a message id. */
    private function assertOwnGroup($student, GroupMessage $message): void
    {
        if ($message->group !== $this->groupChat->studentGroupFor($student)) {
            abort(403, 'Ntabwo uri mu itsinda ryabo.');
        }
    }
}
