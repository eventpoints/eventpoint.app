<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\DataTransferObject\Event\EventDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventMoment;
use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\EventMomentTypeEnum;
use App\Enum\EventParticipantRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventParticpantFactory;
use App\Factory\EventCancellationFactory;
use App\Factory\ImageCollectionFactory;
use App\Factory\ImageFactory;
use App\Factory\UserFactory;
use App\Form\Form\Event\EventCancellationFormType;
use App\Form\Form\Event\EventDetailsFormType;
use App\Form\Form\Event\EventFormType;
use App\Form\Form\Event\EventLocationFormType;
use App\Form\Form\Image\ImageFormType;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Image\ImageCollectionRepository;
use App\Repository\User\UserRepository;
use App\Security\CustomAuthenticator;
use App\Service\EventStatusService;
use App\Service\ImageUploadService\ImageService;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    public function __construct(
            private readonly EventRepository           $eventRepository,
            private readonly ImageService              $imageUploadService,
            private readonly ImageFactory              $imageFactory,
            private readonly ImageCollectionFactory    $imageCollectionFactory,
            private readonly ImageCollectionRepository $imageCollectionRepository,
            private readonly EventParticpantFactory    $eventParticipantFactory,
            private readonly EventFactory              $eventFactory,
            private readonly EventCancellationFactory  $eventCancellationFactory,
            private readonly TranslatorInterface       $translator,
            private readonly EventInvitationRepository $eventInvitationRepository,
            private readonly EventStatusService        $eventStatusService,
            private readonly UserFactory               $userFactory,
            private readonly UserRepository            $userRepository,
            private readonly Security                  $security,
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/', name: 'events')]
    public function index(Request $request): Response
    {
        return $this->render('events/index.html.twig');
    }

    #[Route(path: '/events/create', name: 'create_event')]
    public function create(Request $request, #[CurrentUser] null|User $currentUser): Response
    {
        $eventDto = new EventDto();
        $eventForm = $this->createForm(EventFormType::class, $eventDto);

        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {

            $event = $this->eventFactory->createFromDTOs(
                    eventDto: $eventForm->getData(),
            );

            if (!$currentUser instanceof User) {
                $email = new Email()->setAddress($eventDto->getEmail());
                $currentUser = $this->userFactory->create(firstName: $eventDto->getFirstName(), lastName: $eventDto->getLastName(), email: $email);
                $email->setOwner($currentUser);
                $this->userRepository->save($currentUser, true);

                $this->security->login(user: $currentUser, authenticatorName: CustomAuthenticator::class, firewallName: 'main');
            }

            $event->setOwner($currentUser);
            $participant = $this->eventParticipantFactory->create(
                    owner: $currentUser, event: $event, role: EventParticipantRoleEnum::ROLE_ORGANISER
            );
            $event->addEventParticipant($participant);
            $this->eventRepository->save($event, true);

            return $this->redirectToRoute('show_event', [
                    'id' => $event->getId(),
            ]);
        }

        return $this->render('events/create.html.twig', [
                'form' => $eventForm,
                'eventDto' => $eventDto,
        ]);
    }

    #[Route(path: '/events/edit/{id}', name: 'edit_event')]
    public function edit(Event $event, Request $request): Response
    {
        $eventForm = $this->createForm(EventFormType::class, $event, [
                'event' => $event,
        ]);
        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $image = $eventForm->get('image')->getData();

            if (!empty($image)) {
                /** @var Event $event */
                $event = $eventForm->getData();
                $event->setBase64Image(
                        $this->imageUploadService->processPhoto($image)->getEncoded()
                );
            }

            $this->eventRepository->save(entity: $event, flush: true);

            return $this->redirectToRoute('edit_event', [
                    'id' => $event->getId(),
            ]);
        }

        return $this->render('events/edit.html.twig', [
                'eventForm' => $eventForm,
                'event' => $event,
        ]);
    }

    #[Route(path: '/events/email-invitation/{token}', name: 'event_invitation')]
    public function emailInvitation(string $token): Response
    {
        $invitation = $this->eventInvitationRepository->findByToken(Uuid::fromString($token));

        if (!$invitation instanceof EventInvitation) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('invitation-not-found'));
            return $this->redirectToRoute('app_landing');
        }

        return $this->render('events/email-invitation.html.twig', [
                'emailInvitation' => $invitation,
        ]);
    }

    #[Route(path: '/events/settings/{id}', name: 'event_settings')]
    public function settings(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        return $this->render('events/settings.html.twig', [
                'event' => $event,
        ]);
    }

    #[Route(path: '/events/cancel/{id}', name: 'cancel_event')]
    public function cancelEvent(Event $event, Request $request, #[CurrentUser] User $currentUser): Response
    {
        if (!$this->eventStatusService->can($event, 'cancel')) {
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('event-cannot-be-cancelled'));
            return $this->redirectToRoute('show_event', [
                    'id' => $event->getId(),
            ]);
        }

        $eventCancellation = $this->eventCancellationFactory->create(event: $event, owner: $currentUser);
        $eventCancellationForm = $this->createForm(EventCancellationFormType::class, $eventCancellation);
        $eventCancellationForm->handleRequest($request);
        if ($eventCancellationForm->isSubmitted() && $eventCancellationForm->isValid()) {
            if (CarbonImmutable::now()->diffInMinutes($event->getStartAt()) < 30) {
                $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('too-close-to-event-to-cancel'));
                return $this->redirectToRoute('show_event', [
                        'id' => $event->getId(),
                ]);
            }

            $eventChangeLog = new EventMoment(
                    event: $event,
                    type: EventMomentTypeEnum::EVENT_CANCELED,
                    oldValue: null,
                    newValue: null
            );

            $this->eventStatusService->apply($event, 'cancel');
            $event->setEventCancellation($eventCancellation);
            $event->addEventMoment($eventChangeLog);
            $this->eventRepository->save($event, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('event-canceled'));
        }

        return $this->render('events/cancel.html.twig', [
                'eventCancellationForm' => $eventCancellationForm,
                'event' => $event,
        ]);
    }

    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Request $request, Event $event, #[CurrentUser] null|User $currentUser): Response
    {
        $invitationToken = $request->query->get('token');
        $invitation = null;
        if ($invitationToken) {
            $invitation = $this->eventInvitationRepository->findByToken(Uuid::fromString($invitationToken));
        }

        $imageForm = $this->createForm(ImageFormType::class);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            return $this->handleEventImageUploadForm(imageForm: $imageForm, currentUser: $currentUser, event: $event);
        }

        $invitations = $event->getPendingInvitations();

        if (!empty($event->getUrl())) {
            return $this->render('events/show-external-event.html.twig', [
                    'event' => $event,
            ]);
        }

        return $this->render('events/show.html.twig', [
                'imageForm' => $imageForm,
                'event' => $event,
                'invitation' => $invitation,
                'invitations' => $invitations,
        ]);
    }

    private function handleEventImageUploadForm(FormInterface $imageForm, null|User $currentUser, Event $event): Response
    {
        $UploadedImageFiles = $imageForm->get('images')->getData();
        $images = [];
        foreach ($UploadedImageFiles as $img) {
            $base64 = $this->imageUploadService->processPhoto($img);
            $image = $this->imageFactory->create(dataUrl: $base64->getEncoded());
            $images[] = $image;
        }
        $imageCollection = $this->imageCollectionFactory->create(images: $images, owner: $currentUser, event: $event);
        $this->imageCollectionRepository->save($imageCollection, true);

        $this->addFlash('message', 'images uploaded');
        return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
        ]);
    }


}
