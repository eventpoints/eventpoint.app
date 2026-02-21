<?php

declare(strict_types=1);

namespace App\Controller\Controller\Ticketing;

use App\Entity\Ticketing\TicketMerchantProfile;
use App\Form\Form\Ticketing\TicketMerchantForm;
use App\Repository\Ticketing\TicketMerchantProfileRepository;
use App\Service\Ticketing\StripeConnectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TicketMerchantProfileController extends AbstractController
{
    public function __construct(
        private readonly TicketMerchantProfileRepository $profileRepository,
        private readonly StripeConnectService $stripeConnectService,
    ) {
    }

    #[Route(path: '/ticket-merchant/profile', name: 'merchant_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        $eventId = $request->query->get('event_id');

        $profile = $user->getTicketMerchantProfile() ?? new TicketMerchantProfile($user);

        $form = $this->createForm(TicketMerchantForm::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setTicketMerchantProfile($profile);
            $this->profileRepository->save($profile, true);

            $returnParams = ['id' => (string) $profile->getId()];
            if ($eventId !== null) {
                $returnParams['event_id'] = $eventId;
            }

            $accountId = $this->stripeConnectService->createOrRetrieveAccount($profile);
            $returnUrl  = $this->generateUrl('stripe_connect_return', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);
            $refreshUrl = $this->generateUrl('stripe_connect_refresh', $returnParams, UrlGeneratorInterface::ABSOLUTE_URL);
            $stripeUrl  = $this->stripeConnectService->createAccountLink($accountId, $returnUrl, $refreshUrl);

            return $this->redirect($stripeUrl);
        }

        return $this->render('ticketing/merchant-profile.html.twig', [
            'form' => $form,
            'eventId' => $eventId,
        ]);
    }
}
