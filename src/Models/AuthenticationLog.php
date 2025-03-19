<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthenticationLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'event_name',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'is_successful',
        'event_at',
        'cleared_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_info' => 'array',
        'location' => 'array',
        'is_successful' => 'boolean',
        'event_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return config('sentinel-log.table_name', 'authentication_logs');
    }

    /**
     * Get the authenticatable entity that owns this log.
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }
}