<?php

declare(strict_types=1);

namespace App\Enum;

enum EventParticipantRoleEnum: string
{
    case ROLE_ORGANISER = 'role.event.organiser';
    case ROLE_MODERATOR = 'role.event.moderator';
    case ROLE_PROMOTER = 'role.event.promoter';
    case ROLE_SPONSOR = 'role.event.sponsor';
    case ROLE_PARTICIPANT = 'role.event.participant';
}
