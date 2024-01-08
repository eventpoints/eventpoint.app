<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Enum\FlashEnum;
use App\Form\Form\EmailFormType;
use App\Repository\Event\EventRepository;
use App\Repository\EventEmailInvitationRepository;
use App\Security\Voter\EventVoter;
use App\Service\EventService\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/event/invitations')]
class EventInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventRepository                $eventRepository,
        private readonly EventEmailInvitationRepository $emailInvitationRepository,
        private readonly EventService                   $eventService,
        private readonly TranslatorInterface            $translator
    ) {
    }

    #[Route('/create/{event}', name: 'create_event_invitation', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Event $event, Request $request): Response
    {
        $eventInvitationForm = $this->createForm(EmailFormType::class);
        $eventInvitationForm->handleRequest($request);
        if ($eventInvitationForm->isSubmitted() && $eventInvitationForm->isValid()) {
            $email = $eventInvitationForm->get('email')->getData();

            $this->eventService->process(event: $event, email: $email);

            $this->eventRepository->save($event, true);
            $this->addFlash('message', $this->translator->trans('invitation-sent'));
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/invitation/create.html.twig', [
            'event' => $event,
            'eventInvitationForm' => $eventInvitationForm,
        ]);
    }

    #[Route('/remove/{id}/{token}', name: 'remove_email_invitation', methods: [Request::METHOD_GET])]
    public function remove(EventEmailInvitation $emailInvitation, string $token): Response
    {
        if ($this->isCsrfTokenValid(id: 'remove-invitation', token: $token)) {
            $event = $emailInvitation->getEvent();
            $this->isGranted(EventVoter::EDIT_EVENT, $emailInvitation->getEvent());
            $this->emailInvitationRepository->remove($emailInvitation, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
                '_fragment' => 'invited-users',
            ]);
        }
        return $this->redirectToRoute('app_login');
    }
}
