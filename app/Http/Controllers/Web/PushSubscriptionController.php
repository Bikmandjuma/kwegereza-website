<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Save (or refresh) a browser's push subscription. Public — no auth
     * guard — a push subscription belongs to a device/browser, not an
     * account, so a guest who has never logged in still needs this to
     * work.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $endpointHash = hash('sha256', $request->endpoint);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'endpoint'         => $request->endpoint,
                'public_key'       => $request->input('keys.p256dh'),
                'auth_token'       => $request->input('keys.auth'),
                'content_encoding' => $request->contentEncoding,
                'last_seen_at'     => now(),
            ]
        );

        return response()->json(['status' => 'subscribed']);
    }

    /**
     * A browser unsubscribed locally (or the person disabled notification
     * permission) — remove the now-dead subscription so we stop trying
     * to send to it.
     */
    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint_hash', hash('sha256', $request->endpoint))->delete();

        return response()->json(['status' => 'unsubscribed']);
    }
}
