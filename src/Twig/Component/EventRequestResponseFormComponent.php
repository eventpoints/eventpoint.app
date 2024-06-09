<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\EventRequest;
use App\Factory\Event\EventParticpantFactory;
use App\Factory\Event\EventRejectionFactory;
use App\Repository\Event\EventRepository;
use App\Repository\Event\EventRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('EventRequestResponseFormComponent', template: 'components/event_request_response_form_component.html.twig')]
class EventRequestResponseFormComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public null|EventRequest $eventRequest = null;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventRequestRepository $eventRequestRepository,
        private readonly EventParticpantFactory $eventParticipantFactory,
        private readonly EventRejectionFactory $eventRejectionFactory,
    ) {
    }

    #[LiveAction]
    public function submit(#[LiveArg] bool $isAttending): void
    {
        $owner = $this->eventRequest->getOwner();
        $event = $this->eventRequest->getEvent();
        if ($isAttending) {
            $eventParticipant = $this->eventParticipantFactory->create(owner: $owner, event: $event);
            $event->addEventParticipant($eventParticipant);
        } else {
            $eventRejection = $this->eventRejectionFactory->create(owner: $owner, event: $event);
            $event->addEventRejection($eventRejection);
        }
        $this->eventRepository->save($event, true);
        $this->eventRequestRepository->remove($this->eventRequest, true);
    }
}
