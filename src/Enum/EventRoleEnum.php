<?php

declare(strict_types=1);

namespace App\Enum;

enum EventRoleEnum: string
{
    case ROLE_EVENT_MOD = 'role.event.mod';
    case ROLE_EVENT_MANAGER = 'role.event.manager';
    case ROLE_EVENT_ADMIN = 'role.event.admin';
    case ROLE_EVENT_PROMOTER = 'role.event.promoter';
    case ROLE_EVENT_SPONSOR = 'role.event.sponsor';

    /**
     * @return array<string,string>
     */
    public static function getEventRoles(): array
    {
        return [
            self::ROLE_EVENT_MOD->value => self::ROLE_EVENT_MOD->name,
            self::ROLE_EVENT_MANAGER->value => self::ROLE_EVENT_MANAGER->name,
            self::ROLE_EVENT_ADMIN->value => self::ROLE_EVENT_ADMIN->name,
            self::ROLE_EVENT_PROMOTER->value => self::ROLE_EVENT_PROMOTER->name,
            self::ROLE_EVENT_SPONSOR->value => self::ROLE_EVENT_SPONSOR->name,
        ];
    }
}
