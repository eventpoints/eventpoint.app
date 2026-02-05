<?php

declare(strict_types=1);

namespace App\Enum;

enum EventInvitationTypeEnum: string
{
    case INVITATION = 'invitation';  // Organizer invites someone
    case REQUEST = 'request';        // User requests to join
}
