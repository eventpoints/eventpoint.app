<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\EventEmailInvitation;
use App\Entity\User\Email;
use App\Entity\User\User;

class EventEmailInvitationFactory
{
    public function create(
        Email $email,
        User $owner
    ): EventEmailInvitation {
        $eventEmailInvitation = new EventEmailInvitation();
        $eventEmailInvitation->setEmail($email);
        $eventEmailInvitation->setOwner($owner);
        return $eventEmailInvitation;
    }
}
