<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventOrganiserVoter extends Voter
{
    final public const string REMOVE_EVENT_ORGANISER = 'REMOVE_EVENT_ORGANISER';

    final public const string ADD_EVENT_ORGANISER = 'ADD_EVENT_ORGANISER';

    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::ADD_EVENT_ORGANISER, self::REMOVE_EVENT_ORGANISER], true)
            && $subject instanceof EventParticipant;
    }

    /**
     * @param EventParticipant $subject
     */
    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();
        if (! $currentUser instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::ADD_EVENT_ORGANISER => $this->canAddEventOrganiser($subject, $currentUser),
            self::REMOVE_EVENT_ORGANISER => $this->canRemoveEventOrganiser($subject, $currentUser),
            default => false
        };
    }

    private function canAddEventOrganiser(EventParticipant $participant, User $currentUser): bool
    {
        $event = $participant->getEvent();
        $currentUserParticipant = $event->getEventParticipants()->findFirst(fn (int $key, EventParticipant $p) => $p->getOwner() === $currentUser);
        if (! $currentUserParticipant instanceof EventParticipant) {
            return false;
        }

        if ($currentUserParticipant->getRole() !== EventParticipantRoleEnum::ROLE_ORGANISER) {
            return false;
        }

        if ($participant->getOwner() === $event->getOwner()) {
            return false;
        }

        if ($event->getOrganisers()->count() <= 1) {
            return false;
        }

        return true;
    }

    private function canRemoveEventOrganiser(EventParticipant $participant, User $currentUser): bool
    {
        $event = $participant->getEvent();
        $currentUserParticipant = $event->getEventParticipants()->findFirst(fn (int $key, EventParticipant $p) => $p->getOwner() === $currentUser);
        if (! $currentUserParticipant instanceof EventParticipant) {
            return false;
        }

        if ($participant->getOwner() === $event->getOwner()) {
            return false;
        }

        if ($currentUserParticipant->getRole() !== EventParticipantRoleEnum::ROLE_ORGANISER) {
            return false;
        }

        if ($event->getOrganisers()->count() <= 1) {
            return false;
        }

        return true;
    }
}
