<?php

declare(strict_types=1);

namespace App\Factory\Poll;

use App\Entity\Poll\Poll;
use App\Entity\Poll\PollOption;

final class PollOptionFactory
{
    public function create(
        null|Poll $poll = null,
        null|string $content = null
    ): PollOption {
        $pollOption = new PollOption();
        $pollOption->setPoll($poll);
        $pollOption->setContent($content);
        return $pollOption;
    }

    public function createThreeEmptyOptions(Poll $poll): void
    {
        $counts = [1, 2, 3];
        foreach ($counts as $count) {
            $pollOption = $this->create(poll: $poll);
            $poll->addPollOption($pollOption);
        }
    }
}
