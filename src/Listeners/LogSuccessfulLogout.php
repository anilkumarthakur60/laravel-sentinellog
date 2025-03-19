<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if (!config('sentinel-log.enabled', true) || !config('sentinel-log.events.logout', true)) {
            return;
        }

        AuthenticationLog::create([
            'authenticatable_id' => $event->user->getKey(),
            'authenticatable_type' => get_class($event->user),
            'event_name' => 'logout',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_successful' => true,
        ]);
    }
}