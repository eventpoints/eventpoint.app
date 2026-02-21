<?php

declare(strict_types=1);

namespace App\Enum;

enum TicketStatusEnum: string
{
    case VALID = 'valid';
    case USED = 'used';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
