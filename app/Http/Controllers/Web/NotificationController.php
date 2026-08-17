<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('student')->user();

        $notifications = $user->notifications()->paginate(20);

        return view('Users.User.notifications', compact('notifications'));
    }

    public function markRead($id)
    {
        $user = Auth::guard('student')->user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'url' => $notification->data['url'] ?? null]);
        }

        return back();
    }

    public function markAllRead()
    {
        Auth::guard('student')->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Ibimenyetso byose byasomwe.');
    }

    /**
     * Small JSON endpoint the dashboard bell icon polls for the unread count.
     */
    public function unreadCount()
    {
        $user = Auth::guard('student')->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
