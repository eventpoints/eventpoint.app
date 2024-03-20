<?php

declare(strict_types=1);

namespace App\Factory\EventGroup;

use App\Entity\EventGroup\EventGroupDiscussion;
use App\Entity\EventGroup\EventGroupDiscussionComment;
use App\Entity\User\User;

final class EventGroupDiscussionCommentFactory
{
    public function create(
        null|string $content = null,
        null|EventGroupDiscussion $discussion = null,
        null|User $owner = null
    ): EventGroupDiscussionComment {
        $eventDiscussionComment = new EventGroupDiscussionComment();
        $eventDiscussionComment->setContent($content);
        $eventDiscussionComment->setDiscussion($discussion);
        $eventDiscussionComment->setOwner($owner);

        return $eventDiscussionComment;
    }
}
