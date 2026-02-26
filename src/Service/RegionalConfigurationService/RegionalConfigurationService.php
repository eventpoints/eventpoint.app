<?php

declare(strict_types=1);

namespace App\Service\RegionalConfigurationService;

use App\Entity\User\User;
use App\Enum\RegionalEnum;
use App\Model\RegionalConfiguration;
use Symfony\Component\HttpFoundation\RequestStack;

final class RegionalConfigurationService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function resolveAuthenticatedUserRegionalConfiguration(User $user, RegionalConfiguration $regionalConfiguration): void
    {
        // User preferences take priority; fall back to browser data, then system defaults.
        $regionalConfiguration->setLocale(
            $user->getLocale()
            ?? RegionalEnum::REGIONAL_LOCALE->value
        );

        $regionalConfiguration->setRegion(
            $user->getCountry()
            ?? $regionalConfiguration->getBrowserRegionalData()?->getCountryCode()
            ?? RegionalEnum::REGIONAL_REGION->value
        );

        $regionalConfiguration->setCurrency(
            $user->getCurrency()
            ?? RegionalEnum::REGIONAL_CURRENCY->value
        );

        $regionalConfiguration->setTimezone(
            $regionalConfiguration->getBrowserRegionalData()?->getTimezone()
            ?? RegionalEnum::REGIONAL_TIMEZONE->value
        );
    }

    public function resolveUnauthenticatedUserRegionalConfiguration(RegionalConfiguration $regionalConfiguration): void
    {
        // 1. resolve unauthenticated user locale
        $unauthenticatedUserLocale = $this->resolveUnauthenticatedUserLocale();
        $regionalConfiguration->setLocale($unauthenticatedUserLocale);

        // 2. resolve unauthenticated user currency
        $unauthenticatedUserCurrency = $this->resolveUnauthenticatedUserCurrency();
        $regionalConfiguration->setCurrency($unauthenticatedUserCurrency);

        // 3. resolve unauthenticated user timezone
        $unauthenticatedUserTimezone = $this->resolveUnauthenticatedUserTimezone($regionalConfiguration);
        $regionalConfiguration->setTimezone($unauthenticatedUserTimezone);

        // 4. resolve unauthenticated user region
        $unauthenticatedUserRegion = $this->resolveUnauthenticatedUserRegion($regionalConfiguration);
        $regionalConfiguration->setRegion($unauthenticatedUserRegion);
    }

    public function resolveUnauthenticatedUserLocale(): null|string
    {
        return $this->requestStack->getSession()->get('_locale') ?? RegionalEnum::REGIONAL_LOCALE->value;
    }

    public function resolveUnauthenticatedUserCurrency(): null|string
    {
        return $this->requestStack->getSession()->get('_currency') ?? RegionalEnum::REGIONAL_CURRENCY->value;
    }

    public function resolveUnauthenticatedUserTimezone(RegionalConfiguration $regionalConfiguration): null|string
    {
        return $this->requestStack->getSession()->get('_timezone')
            ?? $regionalConfiguration->getBrowserRegionalData()?->getTimezone()
            ?? RegionalEnum::REGIONAL_TIMEZONE->value;
    }

    private function resolveUnauthenticatedUserRegion(RegionalConfiguration $regionalConfiguration): null|string
    {
        return $this->requestStack->getSession()->get('_region')
            ?? $regionalConfiguration->getBrowserRegionalData()?->getCountryCode()
            ?? RegionalEnum::REGIONAL_REGION->value;
    }
}
