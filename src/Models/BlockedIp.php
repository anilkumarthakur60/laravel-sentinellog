<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $table = 'sentinel_blocked_ips';

    protected $fillable = [
        'ip_address',
        'blocked_at',
        'expires_at',
        'reason',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the IP is currently blocked.
     */
    public function isActive(): bool
    {
        return ! $this->expires_at || $this->expires_at->isFuture();
    }
}
