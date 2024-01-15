<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Email;
use App\Entity\User;
use App\Entity\UserContact;

final class UserContactFactory
{
    public function create(Email $email, User $owner): UserContact
    {
        $userContact = new UserContact();
        $userContact->setEmail($email);
        $userContact->setOwner($owner);
        return $userContact;
    }
}
