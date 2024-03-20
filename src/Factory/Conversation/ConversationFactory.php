<?php

declare(strict_types=1);

namespace App\Factory\Conversation;

use App\Entity\Conversation\Conversation;
use App\Entity\User\User;

final class ConversationFactory
{
    public function create(
        null|User $owner = null
    ): Conversation {
        $conversation = new Conversation();
        $conversation->setOwner($owner);
        return $conversation;
    }
}
