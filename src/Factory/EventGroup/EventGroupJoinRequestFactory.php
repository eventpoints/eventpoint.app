<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupJoinRequest;
use App\Entity\User\User;

final class EventGroupJoinRequestFactory
{
    public function create(
        null|EventGroup $eventGroup = null,
        null|User $owner = null,
    ): EventGroupJoinRequest {
        $eventGroupJoinRequest = new EventGroupJoinRequest();
        $eventGroupJoinRequest->setEventGroup($eventGroup);
        $eventGroupJoinRequest->setOwner($owner);
        return $eventGroupJoinRequest;
    }
}
