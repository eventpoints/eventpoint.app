<?php

declare(strict_types=1);

namespace App\Entity\Contract;

use Carbon\CarbonImmutable;

interface UpdatedAtInterface
{
    public function getUpdatedAt(): CarbonImmutable;

    public function setUpdatedAt(CarbonImmutable $updatedAt): static;
}
