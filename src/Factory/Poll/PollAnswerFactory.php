<?php

declare(strict_types=1);

namespace App\Factory\Poll;

use App\Entity\Poll\Poll;
use App\Entity\Poll\PollAnswer;
use App\Entity\Poll\PollOption;
use App\Entity\User\User;

final class PollAnswerFactory
{
    public function create(
        null|Poll $poll = null,
        null|User $owner = null,
        null|PollOption $pollOption = null,
    ): PollAnswer {
        $pollAnswer = new PollAnswer();
        $pollAnswer->setPoll($poll);
        $pollAnswer->setOwner($owner);
        $pollAnswer->setPollOption($pollOption);
        return $pollAnswer;
    }
}
