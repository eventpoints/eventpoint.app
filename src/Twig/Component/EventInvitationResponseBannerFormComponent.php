<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\EventInvitation;
use App\Factory\Event\EventParticpantFactory;
use App\Factory\Event\EventRejectionFactory;
use App\Repository\Event\EventInvitationRepository;
use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('event_invitation_response_banner_form')]
class EventInvitationResponseBannerFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public null|EventInvitation $eventInvitation = null;

    public function __construct(
        private readonly EventRepository        $eventRepository,
        private readonly EventInvitationRepository $eventInvitationRepository,
        private readonly EventParticpantFactory $eventParticipantFactory,
        private readonly EventRejectionFactory  $eventRejectionFactory,
    ) {
    }

    #[LiveAction]
    public function submit(#[LiveArg] bool $isAttending): void
    {
        $owner = $this->eventInvitation->getTarget();
        $event = $this->eventInvitation->getEvent();
        if ($isAttending) {
            $eventParticipant = $this->eventParticipantFactory->create(owner: $owner, event: $event);
            $event->addEventParticipant($eventParticipant);
        } else {
            $eventRejection = $this->eventRejectionFactory->create(owner: $owner, event: $event);
            $event->addEventRejection($eventRejection);
        }
        $this->eventRepository->save($event, true);
        $this->eventInvitationRepository->remove($this->eventInvitation, true);
    }
}
