<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\EventEmailInvitation;

class EventEmailInvitationFactory
{
    public function create(
        string $email
    ): EventEmailInvitation {
        $eventEmailInvitation = new EventEmailInvitation();
        $eventEmailInvitation->setEmail($email);
        return $eventEmailInvitation;
    }
}
