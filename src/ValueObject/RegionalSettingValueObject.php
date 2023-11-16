<?php

namespace App\ValueObject;

final class RegionalSettingValueObject
{
    private null|string $locale = null;
    private null|string $currency = null;
    private null|string $region = null;
    private null|string $timezone = null;

    /**
     * @param string|null $locale
     * @param string|null $currency
     * @param string|null $region
     * @param string|null $timezone
     */
    public function __construct(
        null|string $locale = null,
        null|string $currency = null,
        null|string $region = null,
        null|string $timezone = null
    )
    {
        $this->locale = $locale;
        $this->currency = $currency;
        $this->region = $region;
        $this->timezone = $timezone;
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

}