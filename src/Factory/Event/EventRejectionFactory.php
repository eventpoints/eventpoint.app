<?php

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\Event\EventRejection;
use App\Entity\User;

final class EventRejectionFactory
{

    /**
     * @param User $owner
     * @param Event $event
     * @return EventRejection
     */
    public function create(
        User  $owner,
        Event $event

    ): EventRejection
    {
        $eventRejection = new EventRejection();
        $eventRejection->setOwner($owner);
        $eventRejection->setEvent($event);

        return $eventRejection;
    }

}