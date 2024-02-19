<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Service\EmailEventService\EmailEventService;
use App\Service\EmailService\EmailToUserConnectorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
readonly class UserCreationSubscriber
{
    public function __construct(
        private EmailEventService           $emailEventService,
        private EmailToUserConnectorService $emailToUserConnectorService,
        private EmailVerifier               $emailVerifier,
    ) {
    }

    public function postPersist(User $user, PostPersistEventArgs $event): void
    {
        // All processes that must happen when a need user object its created

        $this->emailEventService->process(user: $user);
        $this->emailToUserConnectorService->connect(user: $user);

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@eventpoint.app', 'Event Point'))
                ->to($user->getEmail()->getAddress())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('email/confirmation_email.html.twig')
        );
    }
}
