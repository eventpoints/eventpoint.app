<?php

declare(strict_types=1);

namespace App\Service\EmailService;

use App\Entity\Email;
use App\Entity\User;
use App\Repository\EmailRepository;

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
