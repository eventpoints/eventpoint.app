<?php

declare(strict_types=1);

namespace App\Controller\Controller\Ticketing;

use App\Service\Ticketing\StripeConnectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ConnectStripeController extends AbstractController
{
    public function __construct(
        private readonly StripeConnectService $stripeConnectService,
    ) {
    }

    #[Route(path: '/stripe/connect/start', name: 'stripe_connect_start', methods: ['POST', 'GET'])]
    public function start(Request $request): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        $eventId = $request->query->get('event_id');

        $profile = $user->getTicketMerchantProfile();
        if ($profile === null) {
            return $this->redirectToRoute('merchant_profile', array_filter([
                'event_id' => $eventId,
            ]));
        }

        $accountId = $this->stripeConnectService->createOrRetrieveAccount($profile);

        $returnParams = [
            'id' => (string) $profile->getId(),
        ];
        if ($eventId !== null) {
            $returnParams['event_id'] = $eventId;
        }

        $returnUrl = $this->generateUrl('stripe_connect_return', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);
        $refreshUrl = $this->generateUrl('stripe_connect_refresh', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);

        $onboardingUrl = $this->stripeConnectService->createAccountLink($accountId, $returnUrl, $refreshUrl);

        return $this->redirect($onboardingUrl);
    }

    #[Route(path: '/stripe/connect/return/{id}', name: 'stripe_connect_return', methods: ['GET'])]
    public function return(string $id, Request $request): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        $eventId = $request->query->get('event_id');

        $profile = $user->getTicketMerchantProfile();
        if ($profile !== null) {
            $this->stripeConnectService->syncAccountStatus($profile);
        }

        if ($eventId !== null) {
            return $this->redirectToRoute('event_tickets', [
                'id' => $eventId,
            ]);
        }

        return $this->redirectToRoute('merchant_profile');
    }

    #[Route(path: '/stripe/connect/refresh/{id}', name: 'stripe_connect_refresh', methods: ['GET'])]
    public function refresh(string $id, Request $request): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        $eventId = $request->query->get('event_id');

        $profile = $user->getTicketMerchantProfile();
        if ($profile === null) {
            return $this->redirectToRoute('merchant_profile', array_filter([
                'event_id' => $eventId,
            ]));
        }

        $accountId = $this->stripeConnectService->createOrRetrieveAccount($profile);

        $returnParams = [
            'id' => (string) $profile->getId(),
        ];
        if ($eventId !== null) {
            $returnParams['event_id'] = $eventId;
        }

        $returnUrl = $this->generateUrl('stripe_connect_return', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);
        $refreshUrl = $this->generateUrl('stripe_connect_refresh', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);

        $onboardingUrl = $this->stripeConnectService->createAccountLink($accountId, $returnUrl, $refreshUrl);

        return $this->redirect($onboardingUrl);
    }
}
