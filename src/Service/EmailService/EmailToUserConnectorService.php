<?php

declare(strict_types=1);

namespace App\Service\EmailService;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Repository\User\EmailRepository;

final readonly class EmailToUserConnectorService
{
    public function __construct(
        private EmailRepository $emailRepository
    ) {
    }

    public function connect(User $user): null|Email
    {
        $email = $this->emailRepository->findOneBy([
            'address' => $user->getEmail(),
        ]);
        if ($email instanceof Email) {
            $email->setOwner($user);
            $this->emailRepository->save($email, true);
            return $email;
        }

        return null;
    }
}
