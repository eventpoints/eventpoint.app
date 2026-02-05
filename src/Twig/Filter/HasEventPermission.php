<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\Entity\Event\Event;
use App\Entity\Event\EventParticipant;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HasEventPermission extends AbstractExtension
{
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('has_event_permission', fn (null|User $user, Event $event, string $role): null|bool => $this->hasPermission($event, $role, $user)),
        ];
    }

    public function hasPermission(Event $event, string $role, null|User $user = null): null|bool
    {
        $roleEnum = EventParticipantRoleEnum::tryFrom($role);
        $participant = $event->getEventParticipants()->findFirst(fn (int $key, EventParticipant $participant) => $participant->getOwner() === $user);

        if (! $participant instanceof EventParticipant) {
            return false;
        }

        return $participant->getRole() === $roleEnum;
    }
}
