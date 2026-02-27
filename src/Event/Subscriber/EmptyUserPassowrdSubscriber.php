<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'redirectToPasswordForm', priority: 0)]
final readonly class EmptyUserPassowrdSubscriber
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
    ) {
    }

    public function redirectToPasswordForm(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (! $user instanceof User) {
            // not logged in (or not your User class)
            return;
        }


        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route', '');

        // Avoid loops / allow access to the reset form itself
        if ($route === 'reset_user_password') {
            return;
        }

        $password = $user->getPassword();
        if ($password !== null && $password !== '') {
            return;
        }

        $event->setResponse(
            new RedirectResponse($this->router->generate('reset_user_password'))
        );
    }
}
