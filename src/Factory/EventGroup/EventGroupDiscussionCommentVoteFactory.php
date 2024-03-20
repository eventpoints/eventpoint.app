<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroupDiscussionComment;
use App\Entity\EventGroup\EventGroupDiscussionCommentVote;
use App\Entity\User\User;
use App\Enum\VoteEnum;

final class EventGroupDiscussionCommentVoteFactory
{
    public function create(
        null|EventGroupDiscussionComment $eventDiscussionComment = null,
        null|VoteEnum $voteEnum = null,
        null|User $owner = null
    ): EventGroupDiscussionCommentVote {
        $eventDiscussionCommentVote = new EventGroupDiscussionCommentVote();
        $eventDiscussionCommentVote->setDiscussionComment($eventDiscussionComment);
        $eventDiscussionCommentVote->setType($voteEnum);
        $eventDiscussionCommentVote->setOwner($owner);
        return $eventDiscussionCommentVote;
    }
}
