<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User;
use App\Enum\EventOrganiserRoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    final public const EDIT_EVENT = 'EDIT_EVENT';

    final public const PUBLISH_EVENT = 'PUBLISH_EVENT';

    final public const CANCEL_EVENT = 'CANCEL_EVENT';

    public function getCurrentUserEventOrgniser(Event $event, User $currentUser): null|EventOrganiser
    {
        return $event->getEventOrganisers()->findFirst(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $currentUser);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT_EVENT, self::PUBLISH_EVENT, self::CANCEL_EVENT], true)
            && $subject instanceof Event;
    }

    /**
     * @param Event $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();
        if (! $currentUser instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::EDIT_EVENT => $this->canEditEvent($subject, $currentUser),
            self::PUBLISH_EVENT => $this->canPublishEvent($subject, $currentUser),
            self::CANCEL_EVENT => $this->canCancelEvent($subject, $currentUser),
            default => false
        };
    }

    private function canEditEvent(Event $event, User $currentUser): bool
    {
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        $currentUserEventOrgniser = $this->getCurrentUserEventOrgniser($event, $currentUser);
        if (! $currentUserEventOrgniser instanceof EventOrganiser) {
            return false;
        }

        $isPermited = $currentUserEventOrgniser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_ADMIN ||
            $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_MANAGER);

        if (! $isPermited) {
            return false;
        }

        return true;
    }

    private function canPublishEvent(Event $event, User $currentUser): bool
    {
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        $currentUserEventOrgniser = $this->getCurrentUserEventOrgniser($event, $currentUser);
        if (! $currentUserEventOrgniser instanceof EventOrganiser) {
            return false;
        }

        $isPermited = $currentUserEventOrgniser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_ADMIN ||
            $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_MANAGER);

        if (! $isPermited) {
            return false;
        }

        return true;
    }

    private function canCancelEvent(Event $event, User $currentUser): bool
    {
        if ($event->getOwner() === $currentUser) {
            return true;
        }

        $currentUserEventOrgniser = $this->getCurrentUserEventOrgniser($event, $currentUser);
        if (! $currentUserEventOrgniser instanceof EventOrganiser) {
            return false;
        }

        $isPermited = $currentUserEventOrgniser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_ADMIN ||
            $eventRole->getTitle() === EventOrganiserRoleEnum::ROLE_EVENT_MANAGER);

        if (! $isPermited) {
            return false;
        }

        return true;
    }
}
