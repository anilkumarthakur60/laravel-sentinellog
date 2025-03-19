<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        if (!config('sentinel-log.enabled', true) || !config('sentinel-log.events.failed', true)) {
            return;
        }

        AuthenticationLog::create([
            'authenticatable_id' => $event->user ? $event->user->getKey() : null,
            'authenticatable_type' => $event->user ? get_class($event->user) : null,
            'event_name' => 'failed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_successful' => false,
        ]);
    }
}