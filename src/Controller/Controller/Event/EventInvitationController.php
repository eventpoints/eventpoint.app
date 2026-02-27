<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\EventInvitation;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\Event\EventInvitationRepository;
use App\Security\Voter\EventVoter;
use App\Service\MixpanelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/event/invitations')]
class EventInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventInvitationRepository $eventInvitationRepository,
        private readonly TranslatorInterface $translator,
        private readonly EventInvitationFactory $eventInvitationFactory,
        private readonly MixpanelService $mixpanel,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/remove/{id}/{token}', name: 'remove_invitation', methods: [Request::METHOD_GET])]
    public function removeInvitation(EventInvitation $eventInvitation, string $token): Response
    {
        if ($this->isCsrfTokenValid(id: 'remove-invitation', token: $token)) {
            $event = $eventInvitation->getEvent();
            $this->isGranted(EventVoter::EDIT_EVENT, $eventInvitation->getEvent());
            $this->eventInvitationRepository->remove($eventInvitation, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
                '_fragment' => 'invited',
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * Remove an email invitation by token.
     * This handles the legacy route for email invitations.
     *
     * @throws TransportExceptionInterface
     */
    #[Route('/remove/email/{id}/{token}', name: 'remove_email_invitation', methods: [Request::METHOD_GET])]
    public function removeEmailInvitation(EventInvitation $eventInvitation, string $token): Response
    {
        return $this->removeInvitation($eventInvitation, $token);
    }

    /**
     * Find invitation by verification token.
     */
    #[Route('/verify/{invitationToken}', name: 'verify_invitation', methods: [Request::METHOD_GET])]
    public function verifyInvitation(string $invitationToken): Response
    {
        $invitation = $this->eventInvitationRepository->findByToken(Uuid::fromString($invitationToken));

        if (!$invitation instanceof EventInvitation) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('invitation-not-found'));
            return $this->redirectToRoute('app_landing');
        }

        return $this->redirectToRoute('show_event', [
            'id' => $invitation->getEvent()->getId(),
            'token' => $invitationToken,
        ]);
    }

    #[Route('/accept/{id}', name: 'accept_invitation', methods: [Request::METHOD_POST])]
    public function acceptInvitation(EventInvitation $eventInvitation, Request $request, #[CurrentUser] User $currentUser): Response
    {
        if (!$this->isCsrfTokenValid('respond-invitation-' . $eventInvitation->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $this->eventInvitationFactory->accept($eventInvitation);
        $this->eventInvitationRepository->save($eventInvitation, true);
        $this->mixpanel->trackInvitationAccepted($currentUser, $eventInvitation);

        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('accept-invitation'));

        return $this->redirectToRoute('show_event', [
            'id' => $eventInvitation->getEvent()->getId(),
        ]);
    }

    #[Route('/decline/{id}', name: 'decline_invitation', methods: [Request::METHOD_POST])]
    public function declineInvitation(EventInvitation $eventInvitation, Request $request, #[CurrentUser] User $currentUser): Response
    {
        if (!$this->isCsrfTokenValid('respond-invitation-' . $eventInvitation->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $this->eventInvitationFactory->decline($eventInvitation);
        $this->eventInvitationRepository->save($eventInvitation, true);
        $this->mixpanel->trackInvitationDeclined($currentUser, $eventInvitation);

        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('decline-invitation'));

        return $this->redirectToRoute('show_event', [
            'id' => $eventInvitation->getEvent()->getId(),
        ]);
    }
}
