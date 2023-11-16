<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User;

class EventGroupMemberFactory
{
    public function create(User $owner, EventGroup $eventGroup): EventGroupMember
    {
        $eventGroupMember = new EventGroupMember();
        $eventGroupMember->setOwner($owner);
        $eventGroupMember->setEventGroup($eventGroup);

        return $eventGroupMember;
    }
}
