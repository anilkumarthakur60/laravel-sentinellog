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
            'threshold' => 1, // Notify after 1 new device login
        ],
        'failed_attempt' => [
            'enabled' => env('SENTINEL_LOG_NOTIFY_FAILED_ATTEMPT', true),
            'channels' => ['mail'],
            'threshold' => 3, // Notify after 3 failed attempts within a time frame
            'window' => 60, // Time window in minutes for failed attempts
        ],
    ],
];