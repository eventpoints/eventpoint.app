<?php

declare(strict_types=1);

namespace App\Service\ApplicationTimeService;

use Carbon\CarbonImmutable;

final class ApplicationTimeService
{
    public function getNow(): CarbonImmutable
    {
        return CarbonImmutable::now()->setTimezone($_ENV['APP_TIMEZONE']);
    }

    public function getTimezone(): string
    {
        return $_ENV['APP_TIMEZONE'];
    }
}
