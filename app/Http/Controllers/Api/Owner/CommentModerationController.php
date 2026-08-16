<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReport;

class CommentModerationController extends Controller
{
    public function index()
    {
        $reported = CommentReport::with(['comment.user', 'comment.commentable', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('comment_id')
            ->map(fn ($reports, $commentId) => [
                'comment_id' => $commentId,
                'comment' => [
                    'id' => $reports->first()->comment->id,
                    'content' => $reports->first()->comment->content,
                    'status' => $reports->first()->comment->status,
                    'user' => $reports->first()->comment->user?->firstname.' '.$reports->first()->comment->user?->lastname,
                ],
                'reports_count' => $reports->count(),
                'reasons' => $reports->pluck('reason'),
            ])->values();

        $recent = Comment::with(['user', 'commentable'])->latest()->paginate(20);

        return response()->json([
            'success' => true, 'message' => null,
            'data' => [
                'reported' => $reported,
                'recent' => collect($recent->items())->map(fn ($c) => [
                    'id' => $c->id, 'content' => $c->content, 'status' => $c->status,
                    'user' => $c->user ? trim($c->user->firstname.' '.$c->user->lastname) : null,
                    'created_at' => $c->created_at,
                ]),
            ],
            'meta' => ['current_page' => $recent->currentPage(), 'last_page' => $recent->lastPage(), 'total' => $recent->total()],
        ]);
    }

    public function hide(int $id)
    {
        Comment::findOrFail($id)->update(['status' => 'hidden']);
        CommentReport::where('comment_id', $id)->update(['status' => 'resolved']);

        return response()->json(['success' => true, 'message' => 'Igitekerezo cyahishwe.', 'data' => null]);
    }

    public function approve(int $id)
    {
        Comment::findOrFail($id)->update(['status' => 'approved']);
        CommentReport::where('comment_id', $id)->update(['status' => 'resolved']);

        return response()->json(['success' => true, 'message' => 'Igitekerezo cyemejwe.', 'data' => null]);
    }

    public function destroy(int $id)
    {
        Comment::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Igitekerezo cyasibwe burundu.', 'data' => null]);
    }
}
