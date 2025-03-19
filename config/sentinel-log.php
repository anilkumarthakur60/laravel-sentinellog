<?php

declare(strict_types=1);

return [
    'enabled' => env('SENTINEL_LOG_ENABLED', true),
    'events' => [
        'login' => true,
        'logout' => true,
        'failed' => true,
    ],
    'table_name' => 'authentication_logs',
    'prune' => [
        'enabled' => true,
        'days' => 30,
    ],
    'notifications' => [
        'new_device' => [
            'enabled' => env('SENTINEL_LOG_NOTIFY_NEW_DEVICE', true),
            'channels' => ['mail'],
            'threshold' => 1,
        ],
        'failed_attempt' => [
            'enabled' => env('SENTINEL_LOG_NOTIFY_FAILED_ATTEMPT', true),
            'channels' => ['mail'],
            'threshold' => 3,
            'window' => 60,
        ],
        'session_hijacking' => [
            'enabled' => env('SENTINEL_LOG_NOTIFY_HIJACKING', true),
            'channels' => ['mail'],
        ],
    ],
    'two_factor' => [
        'enabled' => env('SENTINEL_LOG_2FA_ENABLED', false),
        'middleware' => 'sentinel-log.2fa',
    ],
    'sessions' => [
        'enabled' => env('SENTINEL_LOG_SESSIONS_ENABLED', true),
        'max_active' => 5, // Max concurrent sessions
    ],
];