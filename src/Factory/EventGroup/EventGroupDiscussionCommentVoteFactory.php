<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventDiscussionComment;
use App\Entity\EventDiscussionCommentVote;
use App\Entity\User;
use App\Enum\VoteEnum;

final class EventGroupDiscussionCommentVoteFactory
{
    public function create(
        null|EventDiscussionComment $eventDiscussionComment = null,
        null|VoteEnum               $voteEnum = null,
        null|User                   $owner = null
    ): EventDiscussionCommentVote {
        $eventDiscussionCommentVote = new EventDiscussionCommentVote();
        $eventDiscussionCommentVote->setDiscussionComment($eventDiscussionComment);
        $eventDiscussionCommentVote->setType($voteEnum);
        $eventDiscussionCommentVote->setOwner($owner);
        return $eventDiscussionCommentVote;
    }
}
