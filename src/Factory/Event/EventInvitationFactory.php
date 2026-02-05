<?php

declare(strict_types=1);

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventParticipant;
use App\Entity\User\Email;
use App\Entity\User\PhoneNumber;
use App\Entity\User\User;
use App\Enum\EventInvitationStatusEnum;
use App\Enum\EventInvitationTypeEnum;
use App\Repository\Event\EventParticipantRepository;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class EventInvitationFactory
{
    public function __construct(
        private EventParticpantFactory $eventParticipantFactory,
        private EventParticipantRepository $eventParticipantRepository,
    ) {
    }

    /**
     * Create a user-to-user invitation.
     */
    public function createUserInvitation(
        User $owner,
        User $target,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        $invitation = new EventInvitation();
        $invitation->setOwner($owner);
        $invitation->setTargetUser($target);
        $invitation->setEvent($event);
        $invitation->setType(EventInvitationTypeEnum::INVITATION);
        $invitation->setStatus(EventInvitationStatusEnum::PENDING);

        if ($createdAt instanceof CarbonImmutable) {
            $invitation->setCreatedAt($createdAt);
        }

        return $invitation;
    }

    /**
     * Create an email invitation (for users without an account).
     */
    public function createEmailInvitation(
        User $owner,
        Email $email,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        $invitation = new EventInvitation();
        $invitation->setOwner($owner);
        $invitation->setTargetEmail($email);
        $invitation->setEvent($event);
        $invitation->setType(EventInvitationTypeEnum::INVITATION);
        $invitation->setStatus(EventInvitationStatusEnum::PENDING);
        $invitation->setToken(Uuid::v4());

        if ($createdAt instanceof CarbonImmutable) {
            $invitation->setCreatedAt($createdAt);
        }

        return $invitation;
    }

    /**
     * Create a phone invitation (for users without an account).
     */
    public function createPhoneInvitation(
        User $owner,
        PhoneNumber $phone,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        $invitation = new EventInvitation();
        $invitation->setOwner($owner);
        $invitation->setTargetPhone($phone);
        $invitation->setEvent($event);
        $invitation->setType(EventInvitationTypeEnum::INVITATION);
        $invitation->setStatus(EventInvitationStatusEnum::PENDING);
        $invitation->setToken(Uuid::v4());

        if ($createdAt instanceof CarbonImmutable) {
            $invitation->setCreatedAt($createdAt);
        }

        return $invitation;
    }

    /**
     * Create a request to join an event (user requesting to attend).
     */
    public function createRequest(
        User $owner,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        $invitation = new EventInvitation();
        $invitation->setOwner($owner);
        $invitation->setEvent($event);
        $invitation->setType(EventInvitationTypeEnum::REQUEST);
        $invitation->setStatus(EventInvitationStatusEnum::PENDING);

        if ($createdAt instanceof CarbonImmutable) {
            $invitation->setCreatedAt($createdAt);
        }

        return $invitation;
    }

    /**
     * Accept an invitation or request and create an EventParticipant.
     */
    public function accept(EventInvitation $invitation): EventParticipant
    {
        $invitation->accept();

        // Determine the user who should become a participant
        $user = $invitation->isRequest()
            ? $invitation->getOwner()
            : $invitation->getResolvedTargetUser();

        $eventParticipant = $this->eventParticipantFactory->create(
            owner: $user,
            event: $invitation->getEvent()
        );

        $this->eventParticipantRepository->save($eventParticipant, true);

        return $eventParticipant;
    }

    /**
     * Decline an invitation or request.
     */
    public function decline(EventInvitation $invitation): void
    {
        $invitation->decline();
    }

    /**
     * @deprecated Use createUserInvitation() instead
     */
    public function create(
        User $owner,
        User $target,
        Event $event,
        null|CarbonImmutable $createdAt = null
    ): EventInvitation {
        return $this->createUserInvitation($owner, $target, $event, $createdAt);
    }

    /**
     * @deprecated Use accept() instead
     */
    public function toEventParticipant(EventInvitation $eventInvitation): EventParticipant
    {
        return $this->eventParticipantFactory->create(
            owner: $eventInvitation->getTargetUser(),
            event: $eventInvitation->getEvent()
        );
    }
}
