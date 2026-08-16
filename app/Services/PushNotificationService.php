<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    /**
     * Sends a push notification to every currently-stored subscription
     * (every browser/device that has ever granted permission — guests,
     * students, and staff alike, since subscriptions aren't tied to a
     * login). Expired or invalid subscriptions (the push service returns
     * 404/410 for those) are removed automatically so the table doesn't
     * accumulate dead entries.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  string  $url  Where to go if the notification is clicked.
     * @return array{sent: int, failed: int, removed: int}
     */
    public function sendToAll(string $title, string $body, string $url): array
    {
        $publicKey = config('webpush.vapid_public_key');
        $privateKey = config('webpush.vapid_private_key');

        if (!$publicKey || !$privateKey) {
            \Log::warning('Push notification skipped: VAPID keys are not configured.');
            return ['sent' => 0, 'failed' => 0, 'removed' => 0];
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('webpush.vapid_subject'),
                'publicKey'  => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'url'   => $url,
        ]);

        $subscriptions = PushSubscription::all();
        $subscriptionsById = [];

        foreach ($subscriptions as $sub) {
            $subscriptionsById[$sub->endpoint_hash] = $sub;

            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys' => [
                        'p256dh' => $sub->public_key,
                        'auth'   => $sub->auth_token,
                    ],
                    'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                ]),
                $payload
            );
        }

        $sent = 0;
        $failed = 0;
        $removed = 0;

        foreach ($webPush->flush() as $report) {
            $hash = hash('sha256', $report->getEndpoint());

            if ($report->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                // 404/410 = the push service says this subscription is
                // permanently gone (browser uninstalled, permission
                // revoked, etc.) — stop trying to send to it.
                $statusCode = $report->getResponse()?->getStatusCode();
                if (in_array($statusCode, [404, 410], true) && isset($subscriptionsById[$hash])) {
                    $subscriptionsById[$hash]->delete();
                    $removed++;
                }
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'removed' => $removed];
    }
}
