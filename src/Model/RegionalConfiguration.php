<?php

declare(strict_types=1);

namespace App\Model;

final class RegionalConfiguration
{
    public function __construct(
        private null|string              $locale = null,
        private null|string              $currency = null,
        private null|string              $region = null,
        private null|string              $timezone = null,
        private null|BrowserRegionalData $browserRegionalData = null,
    ) {
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getBrowserRegionalData(): ?BrowserRegionalData
    {
        return $this->browserRegionalData;
    }

    public function setBrowserRegionalData(?BrowserRegionalData $browserRegionalData): void
    {
        $this->browserRegionalData = $browserRegionalData;
    }
}
