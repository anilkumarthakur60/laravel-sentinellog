<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (!config('sentinel-log.enabled', true) || !config('sentinel-log.events.login', true)) {
            return;
        }

        AuthenticationLog::create([
            'authenticatable_id' => $event->user->getKey(),
            'authenticatable_type' => get_class($event->user),
            'event_name' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_successful' => true,
        ]);
    }
}