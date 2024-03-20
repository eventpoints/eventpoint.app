<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;

final class EventGroupFactory
{
    public function create(
        null|string $title = null,
        null|User $owner = null,
        null|string $language = null,
        null|string $country = null,
        null|string $city = null,
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
