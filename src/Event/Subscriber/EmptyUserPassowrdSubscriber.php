<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class EmptyUserPassowrdSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'redirectToPasswordForm',
        ];
    }

    public function redirectToPasswordForm(RequestEvent $event): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if ($user instanceof UserInterface && $event->isMainRequest()) {
            $request = $event->getRequest();
            $route = $request->attributes->get('_route');

            if ($user->getPassword() === null && $route !== 'reset_user_password') {
                $url = $this->urlGenerator->generate('reset_user_password');
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}
