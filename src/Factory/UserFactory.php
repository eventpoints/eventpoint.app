<?php

declare(strict_types = 1);

namespace App\Factory;

use App\Entity\User;
use App\Service\AvatarService\AvatarService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    final public const PASSWORD_NOT_SET = 'PASSWORD_NOT_SET';

    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function create(string $firstName, string $lastName, string $email, null|string $password): User
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setAvatar($this->avatarService->createAvatar($email));
        if ($password) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        }

        return $user;
    }
}
