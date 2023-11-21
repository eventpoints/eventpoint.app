<?php

declare(strict_types=1);

namespace App\Enum;

enum EventCancellationReasonEnum: string
{
    case EMERGENCY = 'emergency';
    case TECHNICAL_PROBLEM = 'technical-problem';
    case HEALTH_SAFETY_CONCERN = 'health-safety-concern';
    case FORCE_MAJEURE = 'force-majeure';
    case LOGISTICAL_ISSUE = 'logistical-issues';
    case LOW_ATTENDANCE = 'low-attendance';
    case OTHER_PROBLEM = 'other-problem';
}
