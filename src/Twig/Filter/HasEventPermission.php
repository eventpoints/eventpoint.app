<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User;
use App\Enum\EventOrganiserRoleEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HasEventPermission extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('has_event_permission', fn (null|User $user, Event $event, string $role): null|bool => $this->hasPermission($event, $role, $user)),
        ];
    }

    public function hasPermission(Event $event, string $role, null|User $user = null): null|bool
    {
        $roleEnum = EventOrganiserRoleEnum::tryFrom($role);
        $eventOrganiser = $event->getEventOrganisers()->findFirst(fn (int $key, EventOrganiser $eventOrganiser) => $eventOrganiser->getOwner() === $user);

        if (! $eventOrganiser instanceof EventOrganiser) {
            return false;
        }

        return $eventOrganiser->getRoles()->exists(fn (int $key, EventRole $eventRole) => $eventRole->getTitle() === $roleEnum);
    }
}
