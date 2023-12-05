<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User;
use App\Enum\EventOrganiserRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventOrganiserVoter extends Voter
{
    final public const REMOVE_EVENT_ORGANISER = 'REMOVE_EVENT_ORGANISER';

    final public const ADD_EVENT_ORGANISER = 'ADD_EVENT_ORGANISER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::ADD_EVENT_ORGANISER, self::REMOVE_EVENT_ORGANISER], true)
            && $subject instanceof EventOrganiser;
    }

    /**
     * @param EventOrganiser $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
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

    private function canAddEventOrganiser(EventOrganiser $eventOrganiser, User $currentUser): bool
    {
        $event = $eventOrganiser->getEvent();
        $currentUserEventOrganiser = $event->getEventOrganisers()->findFirst(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $currentUser);
        if (! $currentUserEventOrganiser instanceof EventOrganiser) {
            return false;
        }

        $isAdmin = $currentUserEventOrganiser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_ADMIN);

        if (! $isAdmin) {
            return false;
        }

        if ($eventOrganiser->getOwner() === $event->getOwner()) {
            return false;
        }

        if ($event->getEventOrganisers()->count() <= 1) {
            return false;
        }

        return true;
    }

    private function canRemoveEventOrganiser(EventOrganiser $eventOrganiser, User $currentUser): bool
    {
        $event = $eventOrganiser->getEvent();
        $currentUserEventOrganiser = $event->getEventOrganisers()->findFirst(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $currentUser);
        if (! $currentUserEventOrganiser instanceof EventOrganiser) {
            return false;
        }

        $isAdmin = $currentUserEventOrganiser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_ADMIN);

        if ($eventOrganiser->getOwner() === $event->getOwner()) {
            return false;
        }

        if (! $isAdmin) {
            return false;
        }

        if ($event->getEventOrganisers()->count() <= 1) {
            return false;
        }

        return true;
    }
}
