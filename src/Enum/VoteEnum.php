<?php

declare(strict_types=1);

namespace App\Enum;

enum VoteEnum: string
{
    case VOTE_UP = 'up';
    case VOTE_DOWN = 'down';
}
