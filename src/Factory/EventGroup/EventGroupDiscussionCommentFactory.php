<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventDiscussionComment;
use App\Entity\EventGroupDiscussion;
use App\Entity\User;

final class EventGroupDiscussionCommentFactory
{
    public function create(
        null|string $content = null,
        null|EventGroupDiscussion $discussion = null,
        null|User $owner = null
    ): EventDiscussionComment {
        $eventDiscussionComment = new EventDiscussionComment();
        $eventDiscussionComment->setContent($content);
        $eventDiscussionComment->setDiscussion($discussion);
        $eventDiscussionComment->setOwner($owner);

        return $eventDiscussionComment;
    }
}
