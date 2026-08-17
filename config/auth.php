<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'owners',
        ],

        'owner' => [
            'driver' => 'session',
            'provider' => 'owners',
        ],

        // Session-based web guard for registered learners (students), built
        // for browser use — completely separate from 'user'/'api' below,
        // which are JWT and appear to serve an existing mobile client. Both
        // point at the same `users` table/model, but a session login here
        // does not create or consume a JWT, and vice versa.
        'student' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],

        'user' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'owners' => [
            'driver' => 'eloquent',
            'model' => App\Models\Owner::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];