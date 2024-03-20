<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User\Email;
use App\Entity\User\User;

final class EmailFactory
{
    public function create(string $emailAddress, null|User $user = null): Email
    {
        $email = new Email();
        $email->setAddress($emailAddress);
        $email->setOwner($user);
        return $email;
    }
}
