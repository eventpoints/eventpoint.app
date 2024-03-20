<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventParticipant;
use App\Entity\Event\EventRejection;
use App\Entity\User\User;
use Carbon\CarbonImmutable;

final class EventInvitationFactory
{
    public function create(
        User $owner,
        User $target,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        $eventInvite = new EventInvitation();
        $eventInvite->setOwner($owner);
        $eventInvite->setTarget($target);
        $eventInvite->setEvent($event);
        if ($createdAt instanceof CarbonImmutable) {
            $eventInvite->setCreatedAt($createdAt);
        }

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
