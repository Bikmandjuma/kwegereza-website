<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupMessageResource;
use App\Models\GroupMessage;
use App\Services\GroupChatService;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function __construct(private GroupChatService $groupChat)
    {
    }

    public function leadersMessages()
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => GroupMessageResource::collection($this->groupChat->messagesFor(GroupMessage::GROUP_LEADERS)),
            'pinned' => GroupMessageResource::collection($this->groupChat->pinnedFor(GroupMessage::GROUP_LEADERS)),
        ]);
    }

    public function leadersSend(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer',
        ]);

        try {
            $message = $this->groupChat->postAsOwner(
                GroupMessage::GROUP_LEADERS,
                $request->user(),
                $request->message,
                $request->parent_id
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => new GroupMessageResource($message),
        ], 201);
    }

    public function react(Request $request, GroupMessage $message)
    {
        $request->validate(['emoji' => 'required|string|max:16']);

        $type = $this->groupChat->react($message, 'owner', $request->user()->id, $request->emoji);

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ['type' => $type, 'reactions' => $this->groupChat->reactionSummary($message->fresh())],
        ]);
    }

    public function destroy(GroupMessage $message)
    {
        $owner = request()->user();
        $isModerator = $owner->hasPermission('group_chat.moderate');

        try {
            $this->groupChat->delete($message, 'owner', $owner->id, $isModerator);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json(['success' => true, 'message' => 'Ubutumwa bwasibwe.']);
    }

    public function pin(GroupMessage $message)
    {
        $this->groupChat->pin($message, request()->user());

        return response()->json(['success' => true, 'message' => 'Ubutumwa bwomekwe.']);
    }

    public function unpin(GroupMessage $message)
    {
        $this->groupChat->unpin($message);

        return response()->json(['success' => true, 'message' => 'Ubutumwa bworetswe ku rutonde.']);
    }

    public function report(Request $request, GroupMessage $message)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $this->groupChat->report($message, 'owner', $request->user()->id, $request->reason);

        return response()->json(['success' => true, 'message' => 'Raporo yatanzwe.']);
    }

    public function mute(Request $request, string $group)
    {
        $request->validate([
            'actor_type' => 'required|in:student,owner',
            'actor_id' => 'required|integer',
            'muted_until' => 'nullable|date',
        ]);

        $this->groupChat->mute(
            $group,
            $request->actor_type,
            $request->actor_id,
            $request->user(),
            $request->muted_until ? \Carbon\Carbon::parse($request->muted_until) : null
        );

        return response()->json(['success' => true, 'message' => 'Umuntu yaciwe ijambo.']);
    }

    public function unmute(Request $request, string $group)
    {
        $request->validate(['actor_type' => 'required|in:student,owner', 'actor_id' => 'required|integer']);

        $this->groupChat->unmute($group, $request->actor_type, $request->actor_id);

        return response()->json(['success' => true, 'message' => 'Icyo kubuzwa cyavanyweho.']);
    }
}
