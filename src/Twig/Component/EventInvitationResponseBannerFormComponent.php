<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Entity\Event\EventInvitation;
use App\Factory\Event\EventInvitationFactory;
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
        private readonly EventRepository $eventRepository,
        private readonly EventInvitationRepository $eventInvitationRepository,
        private readonly EventInvitationFactory $eventInvitationFactory,
    ) {
    }

    #[LiveAction]
    public function submit(#[LiveArg] bool $isAttending): void
    {
        if ($isAttending) {
            $this->eventInvitationFactory->accept($this->eventInvitation);
        } else {
            $this->eventInvitationFactory->decline($this->eventInvitation);
        }
        $this->eventInvitationRepository->save($this->eventInvitation, true);
    }
}
