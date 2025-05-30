<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthenticationLog extends Model
{
    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'session_id',
        'event_name',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'is_successful',
        'event_at',
        'cleared_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'location' => 'array',
        'is_successful' => 'boolean',
        'event_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('sentinel-log.table_name', 'authentication_logs');
    }

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(SentinelSession::class, 'session_id', 'session_id');
    }
}
