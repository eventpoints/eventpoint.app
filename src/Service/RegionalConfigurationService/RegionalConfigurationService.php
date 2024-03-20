<?php

declare(strict_types=1);

namespace App\Service\RegionalConfigurationService;

use App\Entity\User\User;
use App\Enum\RegionalEnum;
use App\Model\RegionalConfiguration;
use Symfony\Component\HttpFoundation\RequestStack;

class RegionalConfigurationService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function resolveAuthenticatedUserRegionalConfiguration(User $user, RegionalConfiguration $regionalConfiguration): void
    {
        $regionalConfiguration->setLocale($user->getLocale());
        $regionalConfiguration->setRegion($user->getCountry());
        $regionalConfiguration->setCurrency($user->getCurrency());
        $regionalConfiguration->setTimezone($user->getTimezone());
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

        // 2. resolve unauthenticated user currency
        $unauthenticatedUserRegion = $this->resolveUnauthenticatedUserRegion($regionalConfiguration);
        $regionalConfiguration->setRegion($unauthenticatedUserRegion);
    }

    public function resolveUnauthenticatedUserLocale(): null|string
    {
        return $this->requestStack->getSession()->get('_locale') ?? RegionalEnum::REGIONAL_LOCALE->value;
    }

    public function resolveUnauthenticatedUserCurrency(): null|string
    {
        return $this->requestStack->getSession()->get('_currency') ?? RegionalEnum::REGIONAL_CURRECNY->value;
    }

    public function resolveUnauthenticatedUserTimezone(RegionalConfiguration $regionalConfiguration): null|string
    {
        return $this->requestStack->getSession()->get('_timezone') ?? $regionalConfiguration->getBrowserRegionalData()?->getTimezone();
    }

    private function resolveUnauthenticatedUserRegion(RegionalConfiguration $regionalConfiguration): null|string
    {
        return $this->requestStack->getSession()->get('_region') ?? $regionalConfiguration->getBrowserRegionalData()?->getCountryCode();
    }
}
