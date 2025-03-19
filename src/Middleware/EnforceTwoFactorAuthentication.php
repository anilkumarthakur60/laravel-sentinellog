<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Middleware;

use Closure;
use Harryes\SentinelLog\Services\TwoFactorAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceTwoFactorAuthentication
{
    protected TwoFactorAuthenticationService $twoFactorService;

    public function __construct(TwoFactorAuthenticationService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if ($user && $user->two_factor_secret && $user->two_factor_enabled_at) {
            if (!$request->session()->has('2fa_verified')) {
                return redirect()->route('2fa.verify');
            }
        }

        return $next($request);
    }
}