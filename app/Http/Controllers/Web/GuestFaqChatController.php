<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatQuestion;
use App\Models\GuestChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestFaqChatController extends Controller
{
    const FALLBACK = "Ntabwo mbashije kubona igisubizo ku kibazo cyawe.";

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
            'page'     => 'nullable|string|max:255',
        ]);

        $question = trim($request->input('question'));

        $match = ChatQuestion::findBestMatch($question);
        $response = $match ? $match->answer : self::FALLBACK;

        if ($match) {
            $match->increment('times_matched');
        }

        $guestId = $request->cookie('guest_visit_id');

        $sessionId = $request->cookie('faq_session_id');
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            cookie()->queue(cookie('faq_session_id', $sessionId, 60 * 12));
        }

        GuestChatLog::create([
            'guest_id'             => $guestId,
            'session_id'           => $sessionId,
            'ip'                   => $request->ip(),
            'page'                 => $request->input('page'),
            'question'             => $question,
            'response'             => $response,
            'matched_question_id'  => $match?->id,
            'was_matched'          => (bool) $match,
        ]);

        return response()->json([
            'answer'   => $response,
            'matched'  => (bool) $match,
        ]);
    }
}
