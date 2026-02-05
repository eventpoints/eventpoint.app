<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventGroupVoter extends Voter
{
    final public const string VIEW_GROUP = 'VIEW_GROUP';

    final public const string EDIT_GROUP = 'EDIT_GROUP';

    final public const string CREATE_GROUP_POLL = 'CREATE_GROUP_POLL';

    final public const string CREATE_GROUP_DISCUSSION = 'CREATE_GROUP_DISCUSSION';

    final public const string CREATE_GROUP_EVENT = 'CREATE_GROUP_EVENT';

    final public const string ADD_GROUP_MEMBER = 'ADD_GROUP_MEMBER';

    final public const string REMOVE_GROUP_MEMBER = 'REMOVE_GROUP_MEMBER';

    final public const string DELETE_GROUP = 'DELETE_GROUP';

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW_GROUP, self::EDIT_GROUP, self::CREATE_GROUP_EVENT, self::CREATE_GROUP_POLL, self::CREATE_GROUP_DISCUSSION, self::ADD_GROUP_MEMBER, self::REMOVE_GROUP_MEMBER, self::DELETE_GROUP], true)
            && $subject instanceof EventGroup;
    }

    /**
     * @param EventGroup $subject
     */
    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
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
