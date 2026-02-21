<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    final public const string VIEW_EVENT = 'VIEW_EVENT';

    final public const string EDIT_EVENT = 'EDIT_EVENT';

    final public const string CANCEL_EVENT = 'CANCEL_EVENT';

    public function getCurrentUserParticipant(Event $event, User $currentUser): null|EventParticipant
    {
        return $event->getEventParticipants()->findFirst(fn (int $key, EventParticipant $participant) => $participant->getOwner() === $currentUser);
    }

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW_EVENT, self::EDIT_EVENT, self::CANCEL_EVENT], true)
            && $subject instanceof Event;
    }

    /**
     * @param Event $subject
     */
    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();

        // VIEW_EVENT is special: unauthenticated users can view public events
        if ($attribute === self::VIEW_EVENT) {
            return $this->canViewEvent($subject, $currentUser instanceof User ? $currentUser : null);
        }

        if (! $currentUser instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::EDIT_EVENT => $this->canEditEvent($subject, $currentUser),
            self::CANCEL_EVENT => $this->canCancelEvent($subject, $currentUser),
            default => false
        };
    }

    private function canEditEvent(Event $event, User $currentUser): bool
    {
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        $participant = $this->getCurrentUserParticipant($event, $currentUser);
        if (! $participant instanceof EventParticipant) {
            return false;
        }

        return $participant->getRole() === EventParticipantRoleEnum::ROLE_ORGANISER;
    }

    private function canCancelEvent(Event $event, User $currentUser): bool
    {
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        $participant = $this->getCurrentUserParticipant($event, $currentUser);
        if (! $participant instanceof EventParticipant) {
            return false;
        }

        return $participant->getRole() === EventParticipantRoleEnum::ROLE_ORGANISER;
    }

    private function canViewEvent(Event $event, ?User $currentUser): bool
    {
        // Unauthenticated users always see limited details
        if ($currentUser === null) {
            return false;
        }

        // Owner always has access
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        // Any participant (organiser, moderator, sponsor, regular participant) can view
        if ($this->getCurrentUserParticipant($event, $currentUser) instanceof EventParticipant) {
            return true;
        }

        // Users who have been invited (pending or accepted) can view
        if ($event->hasUserInvitation($currentUser)) {
            return true;
        }

        return false;
    }
}
