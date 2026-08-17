<?php

return [
    /*
     * Generated once via Minishlink\WebPush\VAPID::createVapidKeys().
     * The public key is safe to expose in JS (the browser needs it to
     * subscribe). The private key must stay server-side only — never put
     * it in any frontend code or public repo.
     */
    'vapid_public_key'  => env('VAPID_PUBLIC_KEY'),
    'vapid_private_key' => env('VAPID_PRIVATE_KEY'),

    // Required by the Web Push protocol's VAPID claims — identifies who
    // to contact if a push service needs to reach the site operator.
    'vapid_subject' => env('VAPID_SUBJECT', 'mailto:ntiruhungwab@gmail.com'),
];
