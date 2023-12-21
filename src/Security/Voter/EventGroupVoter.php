<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventGroupVoter extends Voter
{
    final public const VIEW_GROUP = 'VIEW_GROUP';

    final public const EDIT_GROUP = 'EDIT_GROUP';

    final public const CREATE_GROUP_POLL = 'CREATE_GROUP_POLL';

    final public const CREATE_GROUP_DISCUSSION = 'CREATE_GROUP_DISCUSSION';

    final public const CREATE_GROUP_EVENT = 'CREATE_GROUP_EVENT';

    final public const ADD_GROUP_MEMBER = 'ADD_GROUP_MEMBER';

    final public const REMOVE_GROUP_MEMBER = 'REMOVE_GROUP_MEMBER';

    final public const DELETE_GROUP = 'DELETE_GROUP';

    public function getCurrentUserEventOrganiser(Event $event, User $currentUser): null|EventOrganiser
    {
        return $event->getEventOrganisers()->findFirst(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $currentUser);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW_GROUP, self::EDIT_GROUP, self::CREATE_GROUP_EVENT, self::CREATE_GROUP_POLL, self::CREATE_GROUP_DISCUSSION, self::ADD_GROUP_MEMBER, self::REMOVE_GROUP_MEMBER, self::DELETE_GROUP], true)
            && $subject instanceof EventGroup;
    }

    /**
     * @param EventGroup $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();
        if (! $currentUser instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW_GROUP => $this->canViewGroup($subject, $currentUser),
            self::EDIT_GROUP => $this->canEditGroup($subject, $currentUser),
            self::DELETE_GROUP => $this->canDeleteGroup($subject, $currentUser),
            default => false
        };
    }

    private function canEditGroup(EventGroup $eventGroup, User $currentUser): bool
    {
        if ($eventGroup->getOwner() === $currentUser || $eventGroup->getIsMaintainer($currentUser)) {
            return true;
        }

        return false;
    }

    private function canDeleteGroup(EventGroup $eventGroup, User $currentUser): bool
    {
        if ($eventGroup->getOwner() === $currentUser || $eventGroup->getIsMaintainer($currentUser)) {
            return true;
        }

        return false;
    }

    private function canViewGroup(EventGroup $eventGroup, User $currentUser): bool
    {
        if ($eventGroup->getOwner() === $currentUser || $eventGroup->getIsMaintainer($currentUser)) {
            return true;
        }

        return false;
    }
}
