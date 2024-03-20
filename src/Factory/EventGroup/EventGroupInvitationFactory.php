<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupInvitation;
use App\Entity\User\User;
use Carbon\CarbonImmutable;

final class EventGroupInvitationFactory
{
    public function create(
        null|EventGroup $eventGroup = null,
        null|User $owner = null,
        null|CarbonImmutable $approvedAt = null,
    ): EventGroupInvitation {
        $eventGroupInvitation = new EventGroupInvitation();
        $eventGroupInvitation->setEventGroup($eventGroup);
        $eventGroupInvitation->setOwner($owner);
        $eventGroupInvitation->setApprovedAt($approvedAt);
        return $eventGroupInvitation;
    }
}
