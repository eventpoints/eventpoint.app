<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User\User;

final class EventOrganiserFactory
{
    /**
     * @param array<int, EventRole> $roles
     */
    public function create(
        User $owner,
        Event $event,
        array $roles
    ): EventOrganiser {
        $eventOrganiser = new EventOrganiser();
        $eventOrganiser->setOwner($owner);
        $eventOrganiser->setEvent($event);

        foreach ($roles as $role) {
            $eventOrganiser->addRole($role);
        }

        return $eventOrganiser;
    }
}
