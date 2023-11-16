<?php

declare(strict_types = 1);

namespace App\Twig\Filter;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HasEventPermission extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('has_event_permission', fn (null|User $user, Event $event, string $role): null|bool => $this->hasPermission($user, $event, $role)),
        ];
    }

    public function hasPermission(null|User $user = null, Event $event, string $role): null|bool
    {
        $eventCrewMember = $event->getEventOrganisers()->findFirst(function (int $key, EventOrganiser $eventOrganiser) use ($user) {
            return $eventOrganiser->getOwner() === $user;
        });

        return $eventCrewMember?->getRoles()->exists(function (int $key, EventRole $eventRole) use ($role){
            return $eventRole->getTitle() === $role;
        });
    }
}
