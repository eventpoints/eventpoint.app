<?php

declare(strict_types=1);

namespace App\Factory\Poll;

use App\Entity\EventGroup\EventGroup;
use App\Entity\Poll\Poll;
use App\Entity\User\User;

final class PollFactory
{
    public function create(
        null|string $prompt = null,
        null|EventGroup $eventGroup = null,
        null|User $owner = null
    ): Poll {
        $poll = new Poll();
        $poll->setPrompt($prompt);
        $poll->setEventGroup($eventGroup);
        $poll->setOwner($owner);
        return $poll;
    }
}
