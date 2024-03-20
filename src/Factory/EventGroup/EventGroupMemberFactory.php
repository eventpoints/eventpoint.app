<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\User\User;

class EventGroupMemberFactory
{
    public function create(null|User $owner = null, null|EventGroup $eventGroup = null, bool $isApproved = false): EventGroupMember
    {
        $eventGroupMember = new EventGroupMember();
        $eventGroupMember->setOwner($owner);
        $eventGroupMember->setEventGroup($eventGroup);
        $eventGroupMember->setIsApproved($isApproved);
        return $eventGroupMember;
    }
}
