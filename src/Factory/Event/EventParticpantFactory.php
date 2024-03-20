<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;

final class EventParticpantFactory
{
    public function create(
        User $owner,
        Event $event
    ): EventParticipant {
        $eventParticipant = new EventParticipant();
        $eventParticipant->setOwner($owner);
        $eventParticipant->setEvent($event);

        return $eventParticipant;
    }
}
