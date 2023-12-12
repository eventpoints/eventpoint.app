<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PhoneNumber;
use App\Entity\User;

final class PhoneNumberFactory
{
    public function create(
        null|string $code = null,
        null|string $number = null,
        null|User   $owner = null,
    ): PhoneNumber {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCode($code);
        $phoneNumber->setNumber($number);
        $phoneNumber->setOwner($owner);
        return $phoneNumber;
    }
}
