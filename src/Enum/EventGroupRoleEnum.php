<?php

namespace App\Enum;

enum EventGroupRoleEnum : string
{
    case ROLE_GROUP_MAINTAINER = 'role.group.maintainer';
    case ROLE_GROUP_MOD = 'role.group.mod';
    case ROLE_GROUP_MANAGER = 'role.group.manager';
    case ROLE_GROUP_CREATOR = 'role.group.creator';
    case ROLE_GROUP_PROMOTER = 'role.group.promoter';
    case ROLE_GROUP_SPONSOR = 'role.group.sponsor';

    public static function getGroupRoles() : array
    {
        return [
            self::ROLE_GROUP_MAINTAINER->value => self::ROLE_GROUP_MAINTAINER->name,
            self::ROLE_GROUP_MOD->value => self::ROLE_GROUP_MOD->name,
            self::ROLE_GROUP_MANAGER->value => self::ROLE_GROUP_MANAGER->name,
            self::ROLE_GROUP_CREATOR->value => self::ROLE_GROUP_CREATOR->name,
            self::ROLE_GROUP_PROMOTER->value => self::ROLE_GROUP_PROMOTER->name,
            self::ROLE_GROUP_SPONSOR->value => self::ROLE_GROUP_SPONSOR->name,
        ];
    }
}
