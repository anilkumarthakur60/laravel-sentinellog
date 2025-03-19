<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Listeners;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Services\DeviceFingerprintService;
use Harryes\SentinelLog\Services\GeolocationService;
use Harryes\SentinelLog\Services\TwoFactorAuthenticationService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    protected DeviceFingerprintService $fingerprintService;
    protected GeolocationService $geoService;
    protected TwoFactorAuthenticationService $twoFactorService;

    public function __construct(
        DeviceFingerprintService $fingerprintService,
        GeolocationService $geoService,
        TwoFactorAuthenticationService $twoFactorService
    ) {
        $this->fingerprintService = $fingerprintService;
        $this->geoService = $geoService;
        $this->twoFactorService = $twoFactorService;
    }

    public function handle(Login $event): void
    {
        if (!config('sentinel-log.enabled', true) || !config('sentinel-log.events.login', true)) {
            return;
        }

        $log = AuthenticationLog::create([
            'authenticatable_id' => $event->user->getKey(),
            'authenticatable_type' => get_class($event->user),
            'event_name' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_info' => $this->fingerprintService->generate(),
            'location' => $this->geoService->getLocation(request()->ip()),
            'is_successful' => true,
        ]);

        if ($event->user->two_factor_secret && !$event->user->session()->has('2fa_verified')) {
            AuthenticationLog::create([
                'authenticatable_id' => $event->user->getKey(),
                'authenticatable_type' => get_class($event->user),
                'event_name' => '2fa_required',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_info' => $this->fingerprintService->generate(),
                'location' => $this->geoService->getLocation(request()->ip()),
                'is_successful' => false,
            ]);
        }

        if ($event->user->isNewDevice($log->device_info['hash'] ?? '')) {
            $event->user->notifyNewDevice($log);
        }
    }
}