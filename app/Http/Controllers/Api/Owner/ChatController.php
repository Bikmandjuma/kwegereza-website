<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatConversationResource;
use App\Http\Resources\ChatMessageResource;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ChatService $chat)
    {
    }

    public function conversations()
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ChatConversationResource::collection($this->chat->conversations()),
        ]);
    }

    public function messages(string $guestId)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ChatMessageResource::collection($this->chat->messagesFor($guestId)),
        ]);
    }

    public function send(Request $request, string $guestId)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $message = $this->chat->sendAsAdmin($guestId, $request->message, $request->user()->firstname);

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => new ChatMessageResource($message),
        ], 201);
    }

    public function markRead(string $guestId)
    {
        $this->chat->markRead($guestId);

        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }
}
