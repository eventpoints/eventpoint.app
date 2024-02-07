<?php

declare(strict_types=1);

namespace App\Model;

final readonly class BrowserRegionalData
{
    public function __construct(
        private null|string $timezone = null,
        private null|int $offsetInMinutes = null,
        private null|string $countryCode = null,
        private null|string $latitude = null,
        private null|string $longitude = null
    ) {
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getOffsetInMinutes(): ?int
    {
        return $this->offsetInMinutes;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }
}
