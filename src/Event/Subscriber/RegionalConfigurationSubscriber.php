<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Entity\User\User;
use App\Model\RegionalConfiguration;
use App\Service\RegionalConfigurationService\RegionalConfigurationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

readonly class RegionalConfigurationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private RegionalConfiguration $regionalConfiguration,
        private RequestStack $requestStack,
        private RegionalConfigurationService $regionalConfigurationService
    ) {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['resolveRegionalConfiguration', 100],
        ];
    }

    public function resolveRegionalConfiguration(RequestEvent $event): void
    {
        // Only run on main request, not sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Don't try to access session if it's not available
        if (!$request->hasSession()) {
            return;
        }

        $user = $this->security->getUser();

        // 1. add browser regional data to regional configuration
        $session = $request->getSession();
        $browserRegionalData = $session->get('browser_regional_data');
        $this->regionalConfiguration->setBrowserRegionalData($browserRegionalData);

        // 2. Check if user is authenticated
        if ($user instanceof User) {
            // 1a. resolve authenticated user regional configuration
            $this->regionalConfigurationService->resolveAuthenticatedUserRegionalConfiguration($user, $this->regionalConfiguration);
        } else {
            // 1b. resolve unauthenticated user regional configuration
            $this->regionalConfigurationService->resolveUnauthenticatedUserRegionalConfiguration($this->regionalConfiguration);
        }

        // 3. add regional configuration to session
        $session->set('regional_configuration', $this->regionalConfiguration);
    }
}
