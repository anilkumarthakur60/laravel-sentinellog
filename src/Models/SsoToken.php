<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SsoToken extends Model
{
    protected $table = 'sentinel_sso_tokens';

    protected $fillable = [
        'authenticatable_id',
        'authenticatable_type',
        'token',
        'client_id',
        'issued_at',
        'expires_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }
}
