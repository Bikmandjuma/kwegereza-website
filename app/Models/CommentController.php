<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
use App\Models\Inyandiko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $inyandikoId)
    {
        $item = Inyandiko::findOrFail($inyandikoId);

        if (!$item->comments_enabled) {
            return back()->with('error', 'Ibisubizo ntabwo byemewe kuri iyi nyandiko.');
        }

        $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'user_id'          => Auth::guard('student')->id(),
            'commentable_id'   => $item->id,
            'commentable_type' => Inyandiko::class,
            'parent_id'        => $request->parent_id,
            'content'          => $request->content,
            'status'           => 'approved', // pre-moderation; reports/hide handle abuse after the fact
        ]);

        return back()->with('success', 'Igitekerezo cyawe cyoherejwe.');
    }

    public function toggleLike($id)
    {
        $comment = Comment::findOrFail($id);
        $userId = Auth::guard('student')->id();

        $existing = CommentLike::where('comment_id', $comment->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CommentLike::create(['comment_id' => $comment->id, 'user_id' => $userId]);
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $comment->likes()->count()]);
    }

    public function report(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        CommentReport::firstOrCreate(
            ['comment_id' => $id, 'reported_by' => Auth::guard('student')->id()],
            ['reason' => $request->reason, 'status' => 'pending']
        );

        return back()->with('success', "Twakiriye raporo yawe, tuzayisuzuma.");
    }

    public function destroy($id)
    {
        $comment = Comment::where('user_id', Auth::guard('student')->id())->findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Igitekerezo cyasibwe.');
    }
}
