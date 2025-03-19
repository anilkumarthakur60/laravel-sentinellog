<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Enable SentinelLog
    |--------------------------------------------------------------------------
    | Enable or disable the entire authentication logging system.
    */
    'enabled' => env('SENTINEL_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Events to Log
    |--------------------------------------------------------------------------
    | Specify which authentication events to log.
    */
    'events' => [
        'login' => true,
        'logout' => true,
        'failed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Storage
    |--------------------------------------------------------------------------
    | Define the table name for storing authentication logs.
    */
    'table_name' => 'authentication_logs',

    /*
    |--------------------------------------------------------------------------
    | Prune Logs
    |--------------------------------------------------------------------------
    | Automatically prune old logs after a set number of days.
    */
    'prune' => [
        'enabled' => true,
        'days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    | Configure notifications for authentication events.
    */
    'notifications' => [
        'new_device' => true,
        'failed_attempt' => true,
        'channels' => ['mail'],
    ],
];