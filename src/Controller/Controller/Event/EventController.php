<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\DataTransferObject\Event\EventDto;
use App\DataTransferObject\EventFilterDto;
use App\DataTransferObject\MapLocationDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventMoment;
use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\CountryCodeEnum;
use App\Enum\EventMomentTypeEnum;
use App\Enum\EventParticipantRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\EmailFactory;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventInvitationFactory;
use App\Factory\Event\EventParticpantFactory;
use App\Factory\EventCancellationFactory;
use App\Factory\ImageCollectionFactory;
use App\Factory\ImageFactory;
use App\Factory\UserFactory;
use App\Form\Filter\EventFilterType;
use App\Form\Form\Event\EventCancellationFormType;
use App\Form\Form\Event\EventDetailsFormType;
use App\Form\Form\Event\EventFormType;
use App\Form\Form\Event\EventLocationFormType;
use App\Form\Form\Image\ImageFormType;
use App\Form\Form\User\RegistrationFormType;
use App\Model\RegionalConfiguration;
use App\Repository\CountryRepository;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Image\ImageCollectionRepository;
use App\Repository\User\EmailRepository;
use App\Repository\User\UserRepository;
use App\Security\CustomAuthenticator;
use App\Service\AvatarService\AvatarService;
use App\Service\EmailEventService\EmailEventService;
use App\Service\EmailService\EmailService;
use App\Service\EventStatusService;
use App\Service\ImageUploadService\ImageUploadService;
use App\Service\MixpanelService;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;

class EventController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
            private readonly EventRepository           $eventRepository,
            private readonly ImageUploadService        $imageUploadService,
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
            private readonly EventGroupRepository      $eventGroupRepository,
            private readonly CountryRepository         $countryRepository,
            private readonly RegionalConfiguration     $regionalConfiguration,
            private readonly AvatarService             $avatarService,
            private readonly EmailFactory              $emailFactory,
            private readonly EmailRepository           $emailRepository,
            private readonly EventInvitationFactory    $eventInvitationFactory,
            private readonly EmailEventService         $emailEventService,
            private readonly EmailService              $emailService,
            private readonly MixpanelService           $mixpanel,
            private readonly MessageBusInterface        $messageBus,
    )
    {
    }

    #[Route(path: '/', name: 'events')]
    public function index(Request $request): Response
    {
        $eventFilterDto = new EventFilterDto();
        $browserCountryCode = $this->regionalConfiguration->getBrowserRegionalData()?->getCountryCode();
        $countryCodeEnum = !empty($browserCountryCode) ? CountryCodeEnum::tryFrom($browserCountryCode) : null;

        $country = null;
        if ($countryCodeEnum !== null) {
            $country = $this->countryRepository->findOneBy([
                    'alpha2' => $countryCodeEnum->value,
            ]);
        }

        if ($country === null) {
            $country = $this->countryRepository->findOneBy([
                    'alpha2' => CountryCodeEnum::CzechRepublic->value,
            ]);
        }

        $eventFilterDto->setCountry($country);
        $eventFilterDto->setCity($country?->getCapitalCity());

        $form = $this->createForm(EventFilterType::class, $eventFilterDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventFilterDto = $form->getData();
        }

        $events = $this->eventRepository->findByFilter(eventFilterDto: $eventFilterDto);
        $groups = $this->eventGroupRepository->findByEventFilter(eventFilterDto: $eventFilterDto);

        $selectedCity = $eventFilterDto->getCity();

        $eventPoints = [];
        foreach ($events as $event) {
            if ($event->getLatitude() && $event->getLongitude()) {
                $eventPoints[] = [
                        'id' => (string)$event->getId(),
                        'lat' => $event->getLatitude(),
                        'lng' => $event->getLongitude(),
                        'title' => $event->getTitle(),
                ];
            }
        }

        $map = new Map(extra: ['events' => $eventPoints]);
        $map->center(new Point($selectedCity?->getLatitude() ?? 50.0755, $selectedCity?->getLongitude() ?? 14.4378));
        $map->zoom(10);
        $map->fitBoundsToMarkers();
        $map->options(
                (new LeafletOptions())
                        ->tileLayer(new TileLayer(
                                url: 'https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=1IDdEWmfCtjKNlJ6Ij3W',
                                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                options: [
                                        'maxZoom' => 25,
                                        'tileSize' => 512,
                                        'zoomOffset' => -1,
                                ]
                        ))
        );

        $boundaryUrl = $selectedCity
                ? $this->generateUrl('api_city_boundary', ['id' => $selectedCity->getId()])
                : null;

        return $this->render('events/index.html.twig', [
                'form' => $form,
                'events' => $events,
                'groups' => $groups,
                'map' => $map,
                'boundaryUrl' => $boundaryUrl,
        ]);
    }

    #[Route(path: '/events/create', name: 'create_event')]
    public function create(Request $request, #[CurrentUser] null|User $currentUser): Response
    {
        $eventDto = new EventDto();

        $map = (new Map('default'))
                ->center(new Point(50.07897895366278, 14.430823454571573))
                ->zoom(12)
                ->fitBoundsToMarkers()
                ->options(
                        (new LeafletOptions())
                                ->tileLayer(new TileLayer(
                                        url: 'https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=1IDdEWmfCtjKNlJ6Ij3W',
                                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                        options: [
                                                'maxZoom' => 25,
                                                'tileSize' => 512,
                                                'zoomOffset' => -1,
                                        ]
                                ))
                );

        $eventForm = $this->createForm(EventFormType::class, $eventDto, [
                'map' => $map,
                'event' => $eventDto
        ]);

        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {

            /** @var EventDto $eventDto */
            $eventDto = $eventForm->getData();

            $locationDto = $eventForm->get('location')->getData();
            if ($locationDto instanceof MapLocationDto) {
                $eventDto->setLatitude($locationDto->getLatitude() !== null ? (string) $locationDto->getLatitude() : null);
                $eventDto->setLongitude($locationDto->getLongitude() !== null ? (string) $locationDto->getLongitude() : null);
                $eventDto->setAddress($locationDto->getAddress());
            }

            $event = $this->eventFactory->createFromDTOs(
                    eventDto: $eventDto,
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

            $this->mixpanel->trackEventCreated($currentUser, $event);

            return $this->redirectToRoute('show_event', [
                    'id' => $event->getId(),
            ]);
        }

        return $this->render('events/create.html.twig', [
                'form' => $eventForm,
                'eventDto' => $eventDto,
                'map' => $map
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
            $imageFile = $event->getImageFile();
            if ($imageFile !== null) {
                $event->setImageFile($this->imageUploadService->processPhoto($imageFile));
            }

            $this->eventRepository->save(entity: $event, flush: true);

            /** @var \App\Entity\User\User $currentUser */
            $currentUser = $this->getUser();
            $this->mixpanel->trackEventUpdated($currentUser, $event);

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
            $this->mixpanel->trackEventCancelled($currentUser, $event);
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

        $canViewDetails = $this->isGranted('VIEW_EVENT', $event);

        // Build registration/request-access forms
        $registrationForm = null;
        $requestAccessForm = null;
        $invitationEmailHasAccount = false;

        if ($currentUser === null) {
            if ($invitation !== null) {
                // Has invitation token: build form to accept the invitation
                $targetEmail = $invitation->getTargetEmail();
                if ($targetEmail?->getOwner() !== null) {
                    $invitationEmailHasAccount = true;
                } else {
                    $registrationForm = $this->createForm(RegistrationFormType::class, new User(), [
                        'action' => $this->generateUrl('app_register', ['_target_path' => $request->getUri()]),
                    ]);
                    $registrationForm->get('email')->setData($targetEmail?->getAddress());
                }
            } elseif (!$canViewDetails) {
                // No invitation token and no access: show sign-up as request-access form
                $requestAccessForm = $this->createForm(RegistrationFormType::class, new User(), [
                    'action' => $this->generateUrl('event_register_and_request', ['id' => $event->getId()]),
                ]);
            }
        }

        // Only build image form for users with full access
        $imageForm = null;
        if ($canViewDetails) {
            $imageForm = $this->createForm(ImageFormType::class);
            $imageForm->handleRequest($request);
            if ($imageForm->isSubmitted() && $imageForm->isValid()) {
                return $this->handleEventImageUploadForm(imageForm: $imageForm, currentUser: $currentUser, event: $event);
            }
        }

        $map = null;
        if ($canViewDetails && $event->getLatitude() && $event->getLongitude()) {
            $lat = (float) $event->getLatitude();
            $lng = (float) $event->getLongitude();

            $map = (new Map())
                ->center(new Point($lat, $lng))
                ->zoom(15)
                ->options(
                    (new LeafletOptions())
                        ->tileLayer(new TileLayer(
                            url: 'https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=1IDdEWmfCtjKNlJ6Ij3W',
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                            options: ['maxZoom' => 25, 'tileSize' => 512, 'zoomOffset' => -1]
                        ))
                );
        }

        return $this->render('events/show.html.twig', [
                'imageForm' => $imageForm,
                'event' => $event,
                'invitation' => $invitation,
                'invitations' => $canViewDetails ? $event->getPendingInvitations() : [],
                'map' => $map,
                'canViewDetails' => $canViewDetails,
                'registrationForm' => $registrationForm,
                'requestAccessForm' => $requestAccessForm,
                'invitationEmailHasAccount' => $invitationEmailHasAccount,
        ]);
    }

    #[Route(path: '/events/{id}/register-and-request', name: 'event_register_and_request', methods: ['POST'])]
    public function registerAndRequest(
        Event $event,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        CustomAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('event_register_and_request', ['id' => $event->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailAddress = $form->get('email')->getData();
            $existingEmail = $this->emailRepository->findOneBy(['address' => $emailAddress]);

            if ($existingEmail instanceof Email && $existingEmail->getOwner() instanceof User) {
                // Email already registered — redirect to sign-in
                $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('email-already-registered'));
                return $this->redirectToRoute('app_login', [
                    '_target_path' => $this->generateUrl('show_event', ['id' => $event->getId()]),
                ]);
            }

            if ($existingEmail instanceof Email) {
                $existingEmail->setOwner($user);
                $user->setEmail($existingEmail);
            } else {
                $email = $this->emailFactory->create(emailAddress: $emailAddress, user: $user);
                $user->setEmail($email);
                $email->setOwner($user);
                $entityManager->persist($email);
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $user->setAvatarFile($this->avatarService->createAvatarFile($emailAddress));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailEventService->process($user);
            $this->emailService->sendRegistrationWelcomeEmail($user->getEmail(), ['user' => $user]);

            // Create a request to attend
            $eventRequest = $this->eventInvitationFactory->createRequest(owner: $user, event: $event);
            $event->addEventInvitation($eventRequest);
            $entityManager->flush();

            $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('show_event', ['id' => $event->getId()]));

            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
        }

        // Form errors — re-render the show page with the form
        return $this->render('events/show.html.twig', [
            'imageForm' => null,
            'event' => $event,
            'invitation' => null,
            'invitations' => [],
            'map' => null,
            'canViewDetails' => false,
            'registrationForm' => null,
            'requestAccessForm' => $form,
            'invitationEmailHasAccount' => false,
        ]);
    }

    private function handleEventImageUploadForm(FormInterface $imageForm, null|User $currentUser, Event $event): Response
    {
        $uploadedFiles = $imageForm->get('images')->getData();
        $images = [];
        foreach ($uploadedFiles as $file) {
            $image = $this->imageFactory->create($file);
            $images[] = $image;
        }
        $imageCollection = $this->imageCollectionFactory->create(images: $images, owner: $currentUser, event: $event);
        $this->imageCollectionRepository->save($imageCollection, true);

        foreach ($images as $image) {
            $this->messageBus->dispatch(new \App\Message\OptimizeEventPhotoMessage((string) $image->getId()));
        }

        $this->addFlash('message', $this->translator->trans('changes-saved'));
        return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
        ]);
    }


}
