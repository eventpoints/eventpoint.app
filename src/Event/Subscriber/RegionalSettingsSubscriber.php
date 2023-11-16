<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use App\Enum\RegionalEnum;
use App\Service\RegionalSettingsService\RegionalSettingsService;
use App\ValueObject\RegionalSettingValueObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RegionalSettingsSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly RegionalSettingsService $regionalSettingsService,
        private readonly Security                $security,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onKernelRequest', 100],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $regionalSettingValueObject = new RegionalSettingValueObject(
                locale: $user->getLocale() ?? RegionalEnum::REGIONAL_LOCALE->value,
                currency: $user->getCurrency() ?? RegionalEnum::REGIONAL_CURRECNY->value,
                region: $user->getCountry() ?? RegionalEnum::REGIONAL_REGION->value,
                timezone: $user->getTimezone() ?? RegionalEnum::REGIONAL_TIMEZONE->value
            );
        }else{
            $regionalSettingValueObject = new RegionalSettingValueObject(
                locale: $event->getRequest()->getSession()->get('_locale') ?? RegionalEnum::REGIONAL_LOCALE->value,
                currency: $event->getRequest()->getSession()->get('_currency') ?? RegionalEnum::REGIONAL_CURRECNY->value,
                region: $event->getRequest()->getSession()->get('_region') ?? RegionalEnum::REGIONAL_REGION->value,
                timezone: $event->getRequest()->getSession()->get('_timezone') ?? RegionalEnum::REGIONAL_TIMEZONE->value
            );
        }
        $this->regionalSettingsService->configure($regionalSettingValueObject);
    }
}
