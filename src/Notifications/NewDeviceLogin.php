<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Notifications;

use Harryes\SentinelLog\Models\AuthenticationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLogin extends Notification
{
    use Queueable;

    protected AuthenticationLog $log;

    public function __construct(AuthenticationLog $log)
    {
        $this->log = $log;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return config('sentinel-log.notifications.new_device.channels', ['mail']);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $location = $this->log->location ?? [];
        return (new MailMessage)
            ->subject('New Device Login Detected')
            ->line('A login was detected from a new device.')
            ->line("IP: {$this->log->ip_address}")
            ->line("Location: {$location['city'] ?? 'Unknown'}, {$location['country'] ?? 'Unknown'}")
            ->line("Time: {$this->log->event_at}")
            ->action('Review Activity', url('/'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'new_device_login',
            'ip_address' => $this->log->ip_address,
            'location' => $this->log->location,
            'event_at' => $this->log->event_at->toDateTimeString(),
        ];
    }
}