<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:comments.moderate');
    }

    public function index()
    {
        $reported = CommentReport::with(['comment.user', 'comment.commentable', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('comment_id');

        $recent = Comment::with(['user', 'commentable'])->latest()->paginate(20);

        return view('Users.admin.comment-moderation', compact('reported', 'recent'));
    }

    public function hide($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'hidden']);
        CommentReport::where('comment_id', $id)->update(['status' => 'resolved']);

        return back()->with('success', 'Igitekerezo cyahishwe.');
    }

    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);
        CommentReport::where('comment_id', $id)->update(['status' => 'resolved']);

        return back()->with('success', 'Igitekerezo cyemejwe.');
    }

    public function destroy($id)
    {
        Comment::findOrFail($id)->delete();

        return back()->with('success', 'Igitekerezo cyasibwe burundu.');
    }
}
