<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroupDiscussion;
use App\Entity\User;

final class EventGroupDiscusionFactroy
{
    public function create(
        null|string     $agenda = null,
        bool            $isResolved = false,
        null|User       $owner = null,
        null|EventGroup $eventGroup = null
    ): EventGroupDiscussion {
        $discussion = new EventGroupDiscussion();
        $discussion->setAgenda($agenda);
        $discussion->setIsResolved($isResolved);
        $discussion->setOwner($owner);
        $discussion->setEventGroup($eventGroup);
        return $discussion;
    }
}
