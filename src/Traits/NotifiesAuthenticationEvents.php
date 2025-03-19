<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Traits;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Notifications\FailedLoginAttempt;
use Harryes\SentinelLog\Notifications\NewDeviceLogin;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

trait NotifiesAuthenticationEvents
{
    use Notifiable;

    /**
     * Get the authentication logs for this model.
     */
    public function authenticationLogs(): MorphMany
    {
        return $this->morphMany(AuthenticationLog::class, 'authenticatable');
    }

    /**
     * Check if this is a new device based on fingerprint hash.
     */
    public function isNewDevice(string $hash): bool
    {
        return !$this->authenticationLogs()
            ->where('is_successful', true)
            ->whereJsonContains('device_info->hash', $hash)
            ->exists();
    }

    /**
     * Notify about a new device login.
     */
    public function notifyNewDevice(AuthenticationLog $log): void
    {
        if (config('sentinel-log.notifications.new_device.enabled', false)) {
            Notification::send($this, new NewDeviceLogin($log));
        }
    }

    /**
     * Notify about repeated failed login attempts.
     */
    public function notifyFailedAttempt(AuthenticationLog $log): void
    {
        if (!config('sentinel-log.notifications.failed_attempt.enabled', false)) {
            return;
        }

        $threshold = config('sentinel-log.notifications.failed_attempt.threshold', 3);
        $window = config('sentinel-log.notifications.failed_attempt.window', 60);

        $recentFailures = $this->authenticationLogs()
            ->where('event_name', 'failed')
            ->where('event_at', '>=', now()->subMinutes($window))
            ->count();

        if ($recentFailures >= $threshold) {
            Notification::send($this, new FailedLoginAttempt($log, $recentFailures));
        }
    }
}