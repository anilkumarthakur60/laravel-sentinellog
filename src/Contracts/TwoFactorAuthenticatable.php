<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Contracts;

use DateTimeInterface;

interface TwoFactorAuthenticatable
{
    public function getTwoFactorSecret(): ?string;

    public function getTwoFactorEnabledAt(): ?DateTimeInterface;
}
