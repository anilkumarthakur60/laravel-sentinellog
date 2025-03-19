<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Services\DeviceFingerprintService;
use Harryes\SentinelLog\Services\GeolocationService;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    protected DeviceFingerprintService $fingerprintService;
    protected GeolocationService $geoService;

    public function __construct(DeviceFingerprintService $fingerprintService, GeolocationService $geoService)
    {
        $this->fingerprintService = $fingerprintService;
        $this->geoService = $geoService;
    }

    public function handle(Failed $event): void
    {
        if (!config('sentinel-log.enabled', true) || !config('sentinel-log.events.failed', true)) {
            return;
        }

        $log = AuthenticationLog::create([
            'authenticatable_id' => $event->user ? $event->user->getKey() : null,
            'authenticatable_type' => $event->user ? get_class($event->user) : null,
            'event_name' => 'failed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_info' => $this->fingerprintService->generate(),
            'location' => $this->geoService->getLocation(request()->ip()),
            'is_successful' => false,
        ]);

        if ($event->user) {
            $event->user->notifyFailedAttempt($log);
        }
    }
}