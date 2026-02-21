<?php

declare(strict_types=1);

namespace App\Enum;

enum UserTokenPurposeEnum: string
{
    case EMAIL_VERIFICATION = 'email_verification';
    case PASSWORD_RESET = 'password_reset';
}
