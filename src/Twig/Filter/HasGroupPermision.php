<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupMember;
use App\Entity\EventGroup\EventGroupRole;
use App\Entity\User\User;
use App\Enum\EventOrganiserRoleEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HasGroupPermision extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('has_group_permission', fn (null|User $user, EventGroup $eventGroup, string $role): null|bool => $this->hasPermission($eventGroup, $role, $user)),
        ];
    }

    public function hasPermission(EventGroup $eventGroup, string $role, null|User $user = null): null|bool
    {
        $roleEnum = EventOrganiserRoleEnum::tryFrom($role);
        $groupMember = $eventGroup->getEventGroupMembers()->findFirst(fn (int $key, EventGroupMember $eventGroupMember) => $eventGroupMember->getOwner() === $user);

        if (! $groupMember instanceof EventGroupMember) {
            return false;
        }

        return $groupMember->getRoles()->exists(fn (int $key, EventGroupRole $eventGroupRole) => $eventGroupRole->getTitle() === $roleEnum);
    }
}
