<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatPresence;

class ChatController extends Controller
{
    public function __construct()
    {
        // Guest-facing methods (sendMessage, messages, presence) are
        // intentionally left ungated here — they run on public routes with
        // no 'ownerAuth' middleware at all, so there is no owner to check a
        // permission against. Only the leader/admin side of Twandikire is
        // gated, per the spec: "Only users with chat.reply permission can
        // respond."
        $this->middleware('permission:chat.view')->only(['ownerchatroom', 'conversations', 'adminMessages']);
        $this->middleware('permission:chat.reply')->only(['adminSend', 'markAsRead']);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'sender_type' => 'required|string',
            'sender_name' => 'required|string',
            'guest_id' => 'required|string',
            'message' => 'required|string',
        ]);

        ChatMessage::create([
            'sender_type' => $request->sender_type,
            'sender_name' => $request->sender_name,
            'guest_id' => $request->guest_id,
            'message' => $request->message,
        ]);

        return response()->json(['success' => true]);
    }

    public function messages($guest_id)
    {
        $messages = ChatMessage::where('guest_id', $guest_id)
            ->orderBy('id')
            ->get();

        return response()->json($messages);
    }

    public function ownerchatroom(){
        return view('Users.admin.chatRoom');
    }

    // public function conversations(){
    //     $conversations = ChatMessage::select(
    //         'guest_id',
    //         'sender_name'
    //     )
    //     ->groupBy(
    //         'guest_id',
    //         'sender_name'
    //     )
    //     ->get();

    //     $conversations->map(function($item){

    //         $presence = ChatPresence::where(
    //             'guest_id',
    //             $item->guest_id
    //         )->first();

    //         $item->online =
    //             $presence &&
    //             now()->diffInSeconds(
    //                 $presence->last_seen
    //             ) < 20;

    //         return $item;
    //     });

    //     return response()->json($conversations);
    // }

    public function conversations(){
        $guests = ChatMessage::select(
            'guest_id',
            'sender_name'
        )
        ->groupBy(
            'guest_id',
            'sender_name'
        )
        ->get();

        $guests->map(function ($guest) {

            $lastMessage = ChatMessage::where(
                'guest_id',
                $guest->guest_id
            )
            ->latest('id')
            ->first();

            $guest->last_message =
                $lastMessage?->message;

            $guest->unread_count =
                ChatMessage::where(
                    'guest_id',
                    $guest->guest_id
                )
                ->where(
                    'sender_type',
                    'guest'
                )
                ->where(
                    'is_read',
                    false
                )
                ->count();

            $presence =
                ChatPresence::where(
                    'guest_id',
                    $guest->guest_id
                )
                ->first();

            $guest->online =
                $presence &&
                now()->diffInSeconds(
                    $presence->last_seen
                ) < 20;

            return $guest;
        });

        return response()->json($guests);
    }


    public function adminMessages($guest_id){
        return response()->json(

            ChatMessage::where(
                'guest_id',
                $guest_id
            )
            ->orderBy('id')
            ->get()

        );
    }

    public function adminSend(Request $request){
        ChatMessage::create([

            'guest_id'    => $request->guest_id,

            'sender_type' => 'admin',

            'sender_name' => Auth::guard('owner')->user()->firstname,

            'message'     => $request->message

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function presence(Request $request)
    {
        ChatPresence::updateOrCreate(

            [
                'guest_id'=>$request->guest_id
            ],

            [
                'last_seen'=>now()
            ]

        );

        return response()->json([
            'success'=>true
        ]);
    }

    public function markAsRead(Request $request)
    {
        ChatMessage::where(
            'guest_id',
            $request->guest_id
        )
        ->where(
            'sender_type',
            'guest'
        )
        ->update([
            'is_read'=>true
        ]);

        return response()->json([
            'success'=>true
        ]);
    }

}