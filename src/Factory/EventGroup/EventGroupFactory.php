<?php

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User;

final class EventGroupFactory
{
    public function create(
        string $title,
        null|User $owner,
    ) : EventGroup
    {
        $eventGroup = new EventGroup();
        $eventGroup->setTitle($title);
        $eventGroup->setOwner($owner);
        return $eventGroup;
    }
}