<?php

declare(strict_types=1);

namespace App\Service\EmailService;

final readonly class EmailHelperService
{
    public function isEmail(string $string): bool
    {
        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }
}
