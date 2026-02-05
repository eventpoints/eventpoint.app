<?php

declare(strict_types=1);

namespace App\Enum;

enum EventStatusEnum: string
{
    case DRAFT = 'draft';
    case AWAITING_REVIEW = 'awaiting_review';
    case PUBLISHED = 'published';
    case ACCEPTING_ADMISSIONS = 'accepting_admissions';
    case ADMISSIONS_CLOSED = 'admissions_closed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::AWAITING_REVIEW => 'Awaiting Review',
            self::PUBLISHED => 'Published',
            self::ACCEPTING_ADMISSIONS => 'Accepting Admissions',
            self::ADMISSIONS_CLOSED => 'Admissions Closed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::AWAITING_REVIEW => 'info',
            self::PUBLISHED => 'primary',
            self::ACCEPTING_ADMISSIONS => 'success',
            self::ADMISSIONS_CLOSED => 'dark',
            self::CANCELLED => 'danger',
        };
    }
}
