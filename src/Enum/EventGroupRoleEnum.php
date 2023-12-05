<?php

declare(strict_types=1);

namespace App\Enum;

enum EventGroupRoleEnum: string
{
    case ROLE_GROUP_MAINTAINER = 'role.group.maintainer';
    case ROLE_GROUP_MOD = 'role.group.mod';
    case ROLE_GROUP_MANAGER = 'role.group.manager';
    case ROLE_GROUP_CREATOR = 'role.group.creator';
    case ROLE_GROUP_PROMOTER = 'role.group.promoter';
    case ROLE_GROUP_SPONSOR = 'role.group.sponsor';
}
