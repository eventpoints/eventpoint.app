<?php

namespace App\DataTransferObject\Event;

use Carbon\CarbonImmutable;

class EventLocationFormDto
{
    private null|string $address = null;

    private null|string $latitude = null;

    private null|string $longitude = null;

    private CarbonImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
