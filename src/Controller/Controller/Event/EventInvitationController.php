<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\EventEmailInvitation;
use App\Entity\Event\EventInvitation;
use App\Enum\FlashEnum;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\EventEmailInvitationRepository;
use App\Security\Voter\EventVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/event/invitations')]
class EventInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventEmailInvitationRepository $emailInvitationRepository,
        private readonly EventInvitationRepository      $eventInvitationRepository,
        private readonly TranslatorInterface            $translator,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/remove/email/{id}/{token}', name: 'remove_email_invitation', methods: [Request::METHOD_GET])]
    public function removeEmailInvitation(EventEmailInvitation $emailInvitation, string $token): Response
    {
        if ($this->isCsrfTokenValid(id: 'remove-invitation', token: $token)) {
            $event = $emailInvitation->getEvent();
            $this->isGranted(EventVoter::EDIT_EVENT, $emailInvitation->getEvent());
            $this->emailInvitationRepository->remove($emailInvitation, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
                '_fragment' => 'invited',
            ]);
        }
        return $this->redirectToRoute('app_login');
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
}
