<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\User;
use App\Enum\FlashEnum;
use App\Factory\Event\EventOrganiserInvitationFactory;
use App\Form\Form\EventOrganiserInviationFormType;
use App\Repository\Event\EventRepository;
use App\Repository\EventOrganiserInvitationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\EventOrganiserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventOrganiserController extends AbstractController
{
    public function __construct(
        private readonly EventRepository                    $eventRepository,
        private readonly EventOrganiserInvitationRepository $eventOrganiserInvitationRepository,
        private readonly UserRepository                     $userRepository,
        private readonly EventOrganiserInvitationFactory    $eventOrganiserInvitationFactory
    ) {
    }

    #[Route(path: '/event/{id}/manage/organisers', name: 'manage_event_organisers')]
    public function index(Event $event): Response
    {
        return $this->render('events/organisers/index.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route(path: '/event/{event}/remove/organiser/{id}', name: 'remove_event_organiser')]
    public function remove(Event $event, EventOrganiser $eventOrganiser): Response
    {
        $this->isGranted(EventOrganiserVoter::REMOVE_EVENT_ORGANISER, $eventOrganiser);
        if ($eventOrganiser->getEvent() === $event) {
            $this->eventRepository->save($event, true);
        }
        return $this->redirectToRoute('manage_event_organisers', [
            'id' => $event->getId(),
        ]);
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
                $eventOrganiserInvitation->setOwner($user);
                $event->addEventOrganiserInvitation($eventOrganiserInvitation);
            } else {
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
}
