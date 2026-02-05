<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;

final class EventParticpantFactory
{
    public function create(
        User $owner,
        Event $event,
        EventParticipantRoleEnum $role = EventParticipantRoleEnum::ROLE_PARTICIPANT,
    ): EventParticipant {
        $eventParticipant = new EventParticipant();
        $eventParticipant->setOwner($owner);
        $eventParticipant->setEvent($event);
        $eventParticipant->setRole($role);

        return $eventParticipant;
    }
}
