<?php

declare(strict_types=1);

namespace App\Enum;

enum EventInvitationStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}
