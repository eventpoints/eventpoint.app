<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User;

final class EventGroupFactory
{
    public function create(
        null|string $title = null,
        null|User $owner = null,
    ): EventGroup {
        $eventGroup = new EventGroup();
        $eventGroup->setName($title);
        $eventGroup->setOwner($owner);
        return $eventGroup;
    }
}
