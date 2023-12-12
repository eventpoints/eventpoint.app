<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\EventOrganiserInvitation;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventOrganiserInvitationFactory;
use App\Form\Form\EventOrganiserInviationFormType;
use App\Repository\EventOrganiserInvitationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\EventVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventOrganiserInvitationController extends AbstractController
{
    public function __construct(
        private readonly EventOrganiserInvitationRepository $eventOrganiserInvitationRepository,
        private readonly UserRepository                     $userRepository,
        private readonly EventOrganiserInvitationFactory    $eventOrganiserInvitationFactory,
        private readonly TranslatorInterface    $translator
    ) {
    }

    #[Route(path: '/event/{id}/invite/organiser', name: 'invite_event_organiser')]
    public function invite(Event $event, Request $request): Response
    {
        $eventOrganiserInvitation = $this->eventOrganiserInvitationFactory->create(event: $event);
        $eventOrganiserInvitationForm = $this->createForm(EventOrganiserInviationFormType::class);
        $eventOrganiserInvitationForm->handleRequest($request);
        if ($eventOrganiserInvitationForm->isSubmitted() && $eventOrganiserInvitationForm->isValid()) {
            $email = $eventOrganiserInvitationForm->get('email')->getData();
            $user = $this->userRepository->findOneBy([
                'email' => $email,
            ]);

            $roles = $eventOrganiserInvitationForm->get('roles')->getData();
            foreach ($roles as $role) {
                $eventOrganiserInvitation->addRole($role);
            }

            if ($user instanceof User) {
                if ($event->isAlreadyInvitedOrganiser($user)) {
                    $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('user-already-invited'));
                    return $this->redirectToRoute('invite_event_organiser', [
                        'id' => $event->getId(),
                    ]);
                }

                $eventOrganiserInvitation->setOwner($user);
                $event->addEventOrganiserInvitation($eventOrganiserInvitation);
            } else {
                if ($event->isEmailAlreadyInvitedOrganiser($email)) {
                    $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('user-already-invited'));
                    return $this->redirectToRoute('invite_event_organiser', [
                        'id' => $event->getId(),
                    ]);
                }

                $eventOrganiserInvitation->setEmail($email);
                $event->addEventOrganiserInvitation($eventOrganiserInvitation);
            }
            $this->eventOrganiserInvitationRepository->save($eventOrganiserInvitation, true);
            $this->addFlash(FlashEnum::MESSAGE->value, 'invitation sent');
            return $this->redirectToRoute('manage_event_organisers', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('events/organisers/invitation.html.twig', [
            'event' => $event,
            'eventOrganiserInvitationForm' => $eventOrganiserInvitationForm,
        ]);
    }

    #[Route(path: '/event/{id}/invite/organiser/remove', name: 'remove_invite_event_organiser', methods: [Request::METHOD_GET])]
    public function delete(EventOrganiserInvitation $eventOrganiserInvitation): Response
    {
        $event = $eventOrganiserInvitation->getEvent();
        $this->isGranted(EventVoter::CANCEL_EVENT, $event);
        $this->eventOrganiserInvitationRepository->remove($eventOrganiserInvitation, true);
        $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('invitation-remove'));
        return $this->redirectToRoute('manage_event_organisers', [
            'id' => $event->getId(),
        ]);
    }
}
