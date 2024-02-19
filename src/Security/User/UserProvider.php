<?php

declare(strict_types=1);

namespace App\Security\User;

use App\Entity\Email;
use App\Entity\PhoneNumber;
use App\Entity\User;
use App\Repository\EmailRepository;
use App\Repository\PhoneNumberRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private EmailRepository       $emailRepository,
        private PhoneNumberRepository $phoneNumberRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $email = $this->emailRepository->findOneBy([
            'address' => $identifier,
        ]);

        if (! $email instanceof Email) {
            $phoneNumber = $this->phoneNumberRepository->findByFullNumber($identifier);
            if ($phoneNumber instanceof PhoneNumber) {
                return $phoneNumber->getOwner();
            }

            throw new UserNotFoundException('User Not Found');
        }

        if (! $email->getOwner() instanceof User) {
            throw new UserNotFoundException('User Not Found');
        }

        return $email->getOwner();
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        $userDefaultEmail = $user->getEmail()->getAddress();
        $refreshedUser = $this->loadUserByIdentifier($userDefaultEmail);

        if (! $refreshedUser instanceof User) {
            throw new UserNotFoundException('User Not Found');
        }

        return $refreshedUser;
    }
}
