<?php

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User;

final class EventOrganiserFactory
{

    /**
     * @param User $owner
     * @param Event $event
     * @param array<int, EventRole> $roles
     * @return EventOrganiser
     */
    public function create(
        User  $owner,
        Event $event,
        array $roles

    ): EventOrganiser
    {
        $eventOrganiser = new EventOrganiser();
        $eventOrganiser->setOwner($owner);
        $eventOrganiser->setEvent($event);

        foreach ($roles as $role) {
            $eventOrganiser->addRole($role);
        }

        return $eventOrganiser;
    }

}