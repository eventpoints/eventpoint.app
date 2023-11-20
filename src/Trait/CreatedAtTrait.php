<?php

declare(strict_types=1);

namespace App\Trait;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private null|CarbonImmutable|DateTimeImmutable $createdAt = null;

    public function getCreatedAt(): null|CarbonImmutable|DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(null|CarbonImmutable|DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
