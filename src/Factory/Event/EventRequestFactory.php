<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventRequest;
use App\Entity\User;

final class EventRequestFactory
{
    public function create(
        null|Event $event = null,
        null|User $owner = null
    ): EventRequest {
        $eventRequest = new EventRequest();
        $eventRequest->setEvent($event);
        $eventRequest->setOwner($owner);
        return $eventRequest;
    }
}
