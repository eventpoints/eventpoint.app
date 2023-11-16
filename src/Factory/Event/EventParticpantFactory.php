<?php

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User;

final class EventParticpantFactory
{

    /**
     * @param User $owner
     * @param Event $event
     * @return EventParticipant
     */
    public function create(
        User  $owner,
        Event $event

    ): EventParticipant
    {
        $eventParticipant = new EventParticipant();
        $eventParticipant->setOwner($owner);
        $eventParticipant->setEvent($event);

        return $eventParticipant;
    }

}