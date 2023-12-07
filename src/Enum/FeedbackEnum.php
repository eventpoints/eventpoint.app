<?php

declare(strict_types=1);

namespace App\Enum;

enum FeedbackEnum: string
{
    case FEATURE_SUGGESTION = 'feature suggestion';
    case NEGATIVE_FEEDBACK = 'negative feedback';
    case POSITIVE_FEEDBACK = 'positive feedback';
    case OTHER = 'other';
}
