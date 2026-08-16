<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatQuestion;
use App\Models\GuestChatLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function __construct()
    {
        // Reuses the existing chat.* permission group rather than inventing
        // faq.* slugs — the guest FAQ chat is part of the same "chat" domain
        // your spec already defines permissions for. If you'd rather split
        // these out later, add faq.view/faq.manage via the Permissions page
        // and swap the strings below — nothing else needs to change.
        $this->middleware('permission:chat.view')->only(['index', 'analytics']);
        $this->middleware('permission:chat.moderate')->only(['store', 'update', 'destroy']);
    }

    /**
     * FAQ knowledge base CRUD
     */
    public function index()
    {
        $questions = ChatQuestion::latest()->paginate(12);

        return view('Users.admin.faq', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:500',
            'language' => 'nullable|string|max:5',
        ]);

        ChatQuestion::create([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'category'   => $request->category,
            'keywords'   => $request->keywords,
            'language'   => $request->language ?: 'rw',
            'status'     => 'active',
            'created_by' => auth('owner')->id(),
        ]);

        return redirect()->route('owner.faq')->with('success', 'Ikibazo n\'igisubizo byashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $item = ChatQuestion::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:500',
            'status'   => 'required|in:active,inactive',
        ]);

        $item->update([
            'question'   => $request->question,
            'answer'     => $request->answer,
            'category'   => $request->category,
            'keywords'   => $request->keywords,
            'status'     => $request->status,
            'updated_by' => auth('owner')->id(),
        ]);

        return redirect()->route('owner.faq')->with('success', 'Byahinduwe neza.');
    }

    public function destroy($id)
    {
        ChatQuestion::findOrFail($id)->delete();

        return redirect()->route('owner.faq')->with('success', 'Ikibazo cyasibwe.');
    }

    /**
     * Guest chat analytics
     */
    public function analytics()
    {
        $totalConversations = GuestChatLog::distinct('session_id')->count('session_id');
        $totalQuestions = GuestChatLog::count();
        $unanswered = GuestChatLog::where('was_matched', false)->count();

        $mostAsked = ChatQuestion::orderByDesc('times_matched')->limit(10)->get(['question', 'times_matched']);

        $unansweredSamples = GuestChatLog::where('was_matched', false)
            ->latest()
            ->limit(20)
            ->get(['question', 'page', 'created_at']);

        $dailyVolume = GuestChatLog::select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $monthlyVolume = GuestChatLog::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('Users.admin.faq-analytics', compact(
            'totalConversations', 'totalQuestions', 'unanswered',
            'mostAsked', 'unansweredSamples', 'dailyVolume', 'monthlyVolume'
        ));
    }
}
