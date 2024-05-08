<?php

declare(strict_types=1);

namespace App\Controller\Controller\Event;

use App\DataTransferObject\Event\EventDetailsFormDto;
use App\DataTransferObject\Event\EventLocationFormDto;
use App\Entity\Event\Event;
use App\Entity\Event\EventEmailInvitation;
use App\Entity\Event\EventMoment;
use App\Entity\User\User;
use App\Enum\EventMomentTypeEnum;
use App\Enum\EventOrganiserRoleEnum;
use App\Enum\FlashEnum;
use App\Factory\Event\EventFactory;
use App\Factory\Event\EventOrganiserFactory;
use App\Factory\EventCancellationFactory;
use App\Factory\ImageCollectionFactory;
use App\Factory\ImageFactory;
use App\Form\Form\Event\EventCancellationFormType;
use App\Form\Form\Event\EventDetailsFormType;
use App\Form\Form\Event\EventFormType;
use App\Form\Form\Event\EventLocationFormType;
use App\Form\Form\Image\ImageFormType;
use App\Repository\Event\EventEmailInvitationRepository;
use App\Repository\Event\EventGroupRepository;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventRoleRepository;
use App\Repository\Image\ImageCollectionRepository;
use App\Service\ImageUploadService\ImageService;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    public const EVENT_FORM_STEP_ONE = 'details';

    public const EVENT_FORM_STEP_TWO = 'location';

    public function __construct(
            private readonly EventRepository                $eventRepository,
            private readonly ImageService                   $imageUploadService,
            private readonly ImageFactory                   $imageFactory,
            private readonly ImageCollectionFactory         $imageCollectionFactory,
            private readonly ImageCollectionRepository      $imageCollectionRepository,
            private readonly EventOrganiserFactory          $eventCrewMemberFactory,
            private readonly EventRoleRepository            $eventRoleRepository,
            private readonly EventFactory                   $eventFactory,
            private readonly EventCancellationFactory       $eventCancellationFactory,
            private readonly TranslatorInterface            $translator,
            private readonly EventEmailInvitationRepository $eventEmailInvitationRepository,
            private readonly EventInvitationRepository      $eventInvitationRepository,
            private readonly RequestStack                   $requestStack,
            private readonly EventGroupRepository           $eventGroupRepository,
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

    #[Route(path: '/events/{id}', name: 'show_event')]
    public function show(Request $request, Event $event, #[CurrentUser] null|User $currentUser): Response
    {
        $invitationToken = $request->get('token');
        $invitation = null;
        if ($invitationToken) {
            $invitation = $this->eventEmailInvitationRepository->findOneBy([
                    'token' => $invitationToken,
            ]);
        }

        $imageForm = $this->createForm(ImageFormType::class);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            return $this->handleEventImageUploadForm(imageForm: $imageForm, currentUser: $currentUser, event: $event);
        }

        $userInvitations = $this->eventEmailInvitationRepository->findByEvent(event: $event);
        $emailInvitations = $this->eventInvitationRepository->findByEvent(event: $event);
        $invitations = new ArrayCollection([...$userInvitations, ...$emailInvitations]);
        $criteria = Criteria::create()->orderBy([
                'createdAt' => Criteria::DESC,
        ]);
        $invitations = $invitations->matching($criteria);

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

    #[Route(path: '/events/email-invitation/{token}', name: 'event_invitation')]
    public function emailInvitation(EventEmailInvitation $emailInvitation): Response
    {
        return $this->render('events/email-invitation.html.twig', [
                'emailInvitation' => $emailInvitation,
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

    #[Route(path: '/events/create/{step}', name: 'create_event')]
    public function create(Request $request, #[CurrentUser] User $currentUser, string $step): Response
    {
        $handle = $this->handleStep($step);
        if ($handle instanceof Response) {
            return $handle;
        }

        $form = match (true) {
            $step == self::EVENT_FORM_STEP_ONE => $this->renderEventFormStepOne($request),
            $step === self::EVENT_FORM_STEP_TWO => $this->renderEventFormStepTwo(),
            default => $this->redirectToRoute('create_event', [
                    'step' => self::EVENT_FORM_STEP_ONE,
            ]),
        };

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return match (true) {
                $step === self::EVENT_FORM_STEP_ONE => $this->handleEventFormStepOne($form),
                $step === self::EVENT_FORM_STEP_TWO => $this->handleEventFormStepTwo($form, $currentUser),
                default => $this->redirectToRoute('create_event', [
                        'step' => self::EVENT_FORM_STEP_ONE,
                ]),
            };
        }

        return $this->render(sprintf('events/create/step-%s.html.twig', $step), [
                'form' => $form,
                'data' => $form->getData(),
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

    private function renderEventFormStepOne(Request $request): FormInterface
    {
        $eventDetailsFormDto = $this->requestStack->getSession()->get('event-form-step-one');


        if (!$eventDetailsFormDto instanceof EventDetailsFormDto) {
            $eventDetailsFormDto = new EventDetailsFormDto();
        }

        $eventGroupId = $request->get('g');
        if (!empty($eventGroupId)) {
            $eventGroup = $this->eventGroupRepository->find($eventGroupId);
            $eventDetailsFormDto->setEventGroup($eventGroup);
        }

        return $this->createForm(EventDetailsFormType::class, $eventDetailsFormDto);
    }

    private function renderEventFormStepTwo(): FormInterface
    {
        $eventLocationFormDto = $this->requestStack->getSession()->get('event-form-step-two');

        if (!$eventLocationFormDto instanceof EventLocationFormDto) {
            $eventLocationFormDto = new EventLocationFormDto();
        }

        return $this->createForm(EventLocationFormType::class, $eventLocationFormDto);
    }

    private function handleEventFormStepOne(FormInterface $form): Response
    {
        if (!empty($form->get('image')->getData())) {
            $base64Image = $this->imageUploadService->processPhoto($form->get('image')->getData());
            $form->getData()->setBase64image($base64Image->getEncoded());
        }

        $this->requestStack->getSession()->set('event-form-step-one', $form->getData());
        return $this->redirectToRoute('create_event', [
                'step' => self::EVENT_FORM_STEP_TWO,
        ]);
    }

    private function handleEventFormStepTwo(FormInterface $form, User $currentUser): Response
    {
        $this->requestStack->getSession()->set('event-form-step-two', $form->getData());

        /** @var EventDetailsFormDto $eventFormDetailsDto */
        $eventFormDetailsDto = $this->requestStack->getSession()->get('event-form-step-one');
        $eventFormLocationDto = $this->requestStack->getSession()->get('event-form-step-two');

        $event = $this->eventFactory->createFromDTOs(
                owner: $currentUser,
                eventFormDetailsDto: $eventFormDetailsDto,
                eventFormLocationDto: $eventFormLocationDto,
        );

        $adminRole = $this->eventRoleRepository->findOneBy([
                'title' => EventOrganiserRoleEnum::ROLE_EVENT_ADMIN,
        ]);
        $eventOrganiser = $this->eventCrewMemberFactory->create(owner: $currentUser, event: $event, roles: [$adminRole]);
        $event->addEventOrganiser($eventOrganiser);

        $this->eventRepository->save($event, true);

        $this->requestStack->getSession()->set('event-form-step-one', null);
        $this->requestStack->getSession()->set('event-form-step-two', null);

        return $this->redirectToRoute('show_event', [
                'id' => $event->getId(),
                '_fragment' => 'invitations',
        ]);
    }

    private function handleStep(string $step): null|Response
    {
        return match ($step) {
            default => null,
            self::EVENT_FORM_STEP_TWO => $this->validateStepOne(),
        };
    }

    private function validateStepOne(): null|Response
    {
        $eventDetailsFormDto = $this->requestStack->getSession()->get('event-form-step-one');

        if (!$eventDetailsFormDto instanceof EventDetailsFormDto) {
            return $this->redirectToRoute('create_event', [
                    'step' => self::EVENT_FORM_STEP_ONE,
            ]);
        }
        return null;
    }
}
