<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;

final class EventGroupFactory
{
    public function create(
        null|string $title = null,
        null|User $owner = null,
        null|string $language = null,
        null|Country $country = null,
        null|City $city = null,
    ): EventGroup {
        $eventGroup = new EventGroup();
        $eventGroup->setName($title);
        $eventGroup->setOwner($owner);
        $eventGroup->setLanguage($language);
        $eventGroup->setCountry($country);
        $eventGroup->setCity($city);
        return $eventGroup;
    }
}
