<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Services;

use Harryes\SentinelLog\Models\SsoToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SsoAuthenticationService
{
    /**
     * Generate an SSO token for a user.
     */
    public function generateToken($authenticatable, string $clientId): string
    {
        $token = Str::random(64);
        SsoToken::create([
            'authenticatable_id' => $authenticatable->getKey(),
            'authenticatable_type' => get_class($authenticatable),
            'token' => $token,
            'client_id' => $clientId,
            'expires_at' => now()->addHours(config('sentinel-log.sso.token_lifetime', 24)),
        ]);

        return $token;
    }

    /**
     * Validate an SSO token and log in the user.
     */
    public function validateToken(string $token, string $clientId): ?object
    {
        $ssoToken = SsoToken::where('token', $token)
            ->where('client_id', $clientId)
            ->first();

        if ($ssoToken && $ssoToken->isValid()) {
            $user = $ssoToken->authenticatable;
            Auth::login($user);
            $ssoToken->delete(); // One-time use
            return $user;
        }

        return null;
    }
}