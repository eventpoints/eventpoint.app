<?php

declare(strict_types=1);

namespace App\Factory\Conversation;

use App\Entity\Conversation\Conversation;
use App\Entity\Conversation\ConversationParticipant;
use App\Entity\User\User;

final class ConversationParticipantFactory
{
    public function create(
        null|User $owner = null,
        null|Conversation $conversation = null
    ): ConversationParticipant {
        $conversationUser = new ConversationParticipant();
        $conversationUser->setOwner($owner);
        $conversationUser->setConversation($conversation);
        return $conversationUser;
    }
}
