<?php

declare(strict_types=1);

namespace App\Service\RegionalSettingsService;

use App\Entity\User;
use App\Enum\RegionalEnum;
use App\ValueObject\RegionalSettingValueObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;
use Twig\Extension\CoreExtension;

final readonly class RegionalSettingsService
{
    private RegionalSettingValueObject $regionalSettingValueObject;

    public function __construct(
        private readonly RequestStack   $requestStack,
        private readonly LocaleSwitcher $localeSwitcher,
    )
    {
        $this->regionalSettingValueObject = new RegionalSettingValueObject();
    }

    public function getRegionalSettingValueObject(): RegionalSettingValueObject
    {
        return $this->regionalSettingValueObject;
    }

    public function setRegionalSettingValueObject(RegionalSettingValueObject $regionalSettingValueObject): void
    {
        $this->regionalSettingValueObject = $regionalSettingValueObject;
    }

    public function configure(RegionalSettingValueObject $regionalSettingValueObject): void
    {
        $this->regionalSettingValueObject->setLocale($regionalSettingValueObject->getLocale());
        $this->localeSwitcher->setLocale($regionalSettingValueObject->getLocale());
        $this->requestStack->getSession()->set('_locale', $regionalSettingValueObject->getLocale());

        $this->regionalSettingValueObject->setRegion($regionalSettingValueObject->getRegion());
        $this->requestStack->getSession()->set('_region', $regionalSettingValueObject->getRegion());

        $this->regionalSettingValueObject->setCurrency($regionalSettingValueObject->getCurrency());
        $this->requestStack->getSession()->set('_currency', $regionalSettingValueObject->getCurrency());

        $this->regionalSettingValueObject->setTimezone($regionalSettingValueObject->getTimezone());
        $this->requestStack->getSession()->set('_timezone', $regionalSettingValueObject->getTimezone());
    }

}
