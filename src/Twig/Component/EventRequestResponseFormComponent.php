<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\EventInvitation;
use App\Enum\EventRequestDeclineReasonEnum;
use App\Factory\Event\EventInvitationFactory;
use App\Repository\Event\EventInvitationRepository;
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
    public null|EventInvitation $eventRequest = null;

    #[LiveProp(writable: true)]
    public bool $showReasonPicker = false;

    public function __construct(
        private readonly EventInvitationRepository $eventInvitationRepository,
        private readonly EventInvitationFactory $eventInvitationFactory,
    ) {
    }

    /**
     * @return EventRequestDeclineReasonEnum[]
     */
    public function getDeclineReasons(): array
    {
        return EventRequestDeclineReasonEnum::cases();
    }

    #[LiveAction]
    public function toggleReasonPicker(): void
    {
        $this->showReasonPicker = ! $this->showReasonPicker;
    }

    #[LiveAction]
    public function accept(): void
    {
        $this->eventInvitationFactory->accept($this->eventRequest);
        $this->eventInvitationRepository->save($this->eventRequest, true);
    }

    #[LiveAction]
    public function confirmDecline(#[LiveArg] string $reason): void
    {
        $this->eventInvitationFactory->decline($this->eventRequest, EventRequestDeclineReasonEnum::from($reason));
        $this->eventInvitationRepository->save($this->eventRequest, true);
        $this->showReasonPicker = false;
    }
}
