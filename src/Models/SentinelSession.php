<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SentinelSession extends Model
{
    protected $table = 'sentinel_sessions';

    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'session_id',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'last_activity',
    ];

    protected $casts = [
        'device_info' => 'array',
        'location' => 'array',
        'last_activity' => 'datetime',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AuthenticationLog::class, 'session_id', 'session_id');
    }
}
