<?php

declare(strict_types=1);

namespace App\Enum;

enum ExternalEventStatusEnum: string
{
    case PENDING  = 'pending';   // newly scraped, awaiting review
    case APPROVED = 'approved';  // visible on platform
    case REJECTED = 'rejected';  // not suitable / duplicate
}
