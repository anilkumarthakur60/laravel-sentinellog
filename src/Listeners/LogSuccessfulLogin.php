<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Services\DeviceFingerprintService;
use Harryes\SentinelLog\Services\GeolocationService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    protected DeviceFingerprintService $fingerprintService;
    protected GeolocationService $geoService;

    public function __construct(DeviceFingerprintService $fingerprintService, GeolocationService $geoService)
    {
        $this->fingerprintService = $fingerprintService;
        $this->geoService = $geoService;
    }

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
            'device_info' => $this->fingerprintService->generate(),
            'location' => $this->geoService->getLocation(request()->ip()),
            'is_successful' => true,
        ]);
    }
}