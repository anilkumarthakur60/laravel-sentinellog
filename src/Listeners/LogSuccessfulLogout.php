<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Services\DeviceFingerprintService;
use Harryes\SentinelLog\Services\GeolocationService;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    protected DeviceFingerprintService $fingerprintService;
    protected GeolocationService $geoService;

    public function __construct(DeviceFingerprintService $fingerprintService, GeolocationService $geoService)
    {
        $this->fingerprintService = $fingerprintService;
        $this->geoService = $geoService;
    }

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
            'device_info' => $this->fingerprintService->generate(),
            'location' => $this->geoService->getLocation(request()->ip()),
            'is_successful' => true,
        ]);
    }
}