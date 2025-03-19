<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Notifications;

use Harryes\SentinelLog\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionHijackingDetected extends Notification
{
    use Queueable;

    protected Session $session;
    protected string $reason;

    public function __construct(Session $session, string $reason)
    {
        $this->session = $session;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return config('sentinel-log.notifications.session_hijacking.channels', ['mail']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $location = $this->session->location ?? [];
        $city = $location['city'] ?? 'Unknown';
        $country = $location['country'] ?? 'Unknown';

        return (new MailMessage)
            ->subject('Potential Session Hijacking Detected')
            ->line('We detected suspicious activity on your account.')
            ->line("Reason: {$this->reason}")
            ->line("IP: {$this->session->ip_address}")
            ->line("Location: {$city}, {$country}")
            ->line("Last Activity: {$this->session->last_activity}")
            ->action('Review Sessions', url('/sessions'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'session_hijacking',
            'session_id' => $this->session->session_id,
            'ip_address' => $this->session->ip_address,
            'location' => $this->session->location,
            'reason' => $this->reason,
        ];
    }
}