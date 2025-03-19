<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Services;

use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base32;

class TwoFactorAuthenticationService
{
    /**
     * Generate a new 2FA secret.
     */
    public function generateSecret(): string
    {
        return Base32::encodeUpper(Str::random(16));
    }

    /**
     * Generate a TOTP code for a secret.
     */
    public function generateCode(string $secret, int $timestamp = null): string
    {
        $secret = Base32::decodeUpper($secret);
        $timestamp = $timestamp ?? floor(time() / 30); // 30-second window

        $binary = pack('N*', 0) . pack('N*', $timestamp);
        $hash = hash_hmac('sha1', $binary, $secret, true);
        $offset = ord($hash[19]) & 0x0f;

        $code = (unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff) % 1000000;
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a TOTP code against a secret.
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $timestamp = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $expected = $this->generateCode($secret, $timestamp + $i);
            if (hash_equals($expected, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a QR code URL for authenticator apps.
     */
    public function getQrCodeUrl(string $secret, string $email, string $issuer = 'SentinelLog'): string
    {
        $label = urlencode("{$issuer}:{$email}");
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }
}