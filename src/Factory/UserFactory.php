<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Service\AvatarService\AvatarService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    final public const string PASSWORD_NOT_SET = 'PASSWORD_NOT_SET';

    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function create(string $firstName, string $lastName, Email $email, null|string $password = null, null|string $avatarUrl = null): User
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $email->setOwner($user);
        $user->setEmail($email);
        if ($avatarUrl !== null) {
            $user->setAvatarUrl($avatarUrl);
        } else {
            $user->setAvatarFile($this->avatarService->createAvatarFile($email->getAddress()));
        }
        if ($password) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        }

        return $user;
    }
}
