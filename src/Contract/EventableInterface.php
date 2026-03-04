<?php

declare(strict_types=1);

namespace App\Contract;

use Carbon\CarbonImmutable;

interface EventableInterface
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getStartAt(): ?CarbonImmutable;

    public function getEndAt(): ?CarbonImmutable;

    public function getVenueName(): ?string;

    public function getAddress(): ?string;

    public function getLatitude(): ?string;

    public function getLongitude(): ?string;
}
