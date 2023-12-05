<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\EventOrganiserInvitation;
use App\Entity\User;

final class EventOrganiserInvitationFactory
{
    public function create(
        null|Event  $event = null,
        null|string $email = null,
        null|User   $owner = null,
    ): EventOrganiserInvitation {
        $eventOrganiserInvitation = new EventOrganiserInvitation();
        $eventOrganiserInvitation->setEvent($event);
        $eventOrganiserInvitation->setEmail($email);
        $eventOrganiserInvitation->setOwner($owner);

        return $eventOrganiserInvitation;
    }
}
