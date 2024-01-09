<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventParticipant;
use App\Entity\Event\EventRejection;
use App\Entity\User;

final class EventInvitationFactory
{
    public function create(
        User  $owner,
        User  $target,
        Event $event
    ): EventInvitation {
        $eventInvite = new EventInvitation();
        $eventInvite->setOwner($owner);
        $eventInvite->setTarget($target);
        $eventInvite->setEvent($event);

        return $eventInvite;
    }

    public function toEventParticipant(EventInvitation $eventInvitation): EventParticipant
    {
        $eventParticipantFactory = new EventParticpantFactory();
        return $eventParticipantFactory->create(
            owner: $eventInvitation->getTarget(),
            event: $eventInvitation->getEvent()
        );
    }

    public function toEventRejection(EventInvitation $eventInvitation): EventRejection
    {
        $eventRejectionFactory = new EventRejectionFactory();
        return $eventRejectionFactory->create(
            owner: $eventInvitation->getTarget(),
            event: $eventInvitation->getEvent()
        );
    }
}
