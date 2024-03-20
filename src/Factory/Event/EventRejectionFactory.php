<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventRejection;
use App\Entity\User\User;

final class EventRejectionFactory
{
    public function create(
        User $owner,
        Event $event
    ): EventRejection {
        $eventRejection = new EventRejection();
        $eventRejection->setOwner($owner);
        $eventRejection->setEvent($event);

        return $eventRejection;
    }
}
