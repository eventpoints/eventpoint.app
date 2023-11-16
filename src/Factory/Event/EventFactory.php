<?php

namespace App\Factory\Event;

use App\Entity\Event\Event;
use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventInvitation;
use App\Entity\Event\EventParticipant;
use App\Entity\Event\EventRejection;
use App\Entity\Event\EventRequest;
use DateTimeImmutable;

final class EventFactory
{

    public function create(
        string            $title,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt,
        string            $base64Image,
        string            $latitude,
        string            $longitude,
        string            $description,
        bool              $isPrivate
    ): Event
    {
        $event = new Event();
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setStartAt($startAt);
        $event->setEndAt($endAt);
        $event->setBase64Image($base64Image);
        $event->setLatitude($latitude);
        $event->setLongitude($longitude);
        $event->setIsPrivate($isPrivate);

        return $event;
    }

    /**
     * @param array<int,EventOrganiser> $eventCrewMembers
     * @param Event $event
     * @return void
     */
    public function addEventOrganisers(array $eventCrewMembers, Event $event): void
    {
        foreach ($eventCrewMembers as $eventCrewMember) {
            $eventCrewMember->setEvent($event);
            $event->addEventCrewMember($eventCrewMember);
        }
    }

    /**
     * @param array<int,EventParticipant> $eventParticipants
     * @param Event $event
     * @return void
     */
    public function addEventParticipants(array $eventParticipants, Event $event): void
    {
        foreach ($eventParticipants as $eventParticipant) {
            $eventParticipant->setEvent($event);
            $event->addEventParticipant($eventParticipant);
        }
    }

    /**
     * @param array<int,EventInvitation> $eventInvites
     * @param Event $event
     * @return void
     */
    public function addEventInvitations(array $eventInvites, Event $event): void
    {
        foreach ($eventInvites as $eventInvite) {
            $eventInvite->setEvent($event);
            $event->addEventInvite($eventInvite);
        }
    }

    /**
     * @param array<int,EventRequest> $eventRequests
     * @param Event $event
     * @return void
     */
    public function addEventRequests(array $eventRequests, Event $event): void
    {
        foreach ($eventRequests as $eventRequest) {
            $eventRequest->setEvent($event);
            $event->addEventRequest($eventRequest);
        }
    }

    /**
     * @param array<int,EventRejection> $eventRejections
     * @param Event $event
     * @return void
     */
    public function addEventRejections(array $eventRejections, Event $event): void
    {
        foreach ($eventRejections as $eventRejection) {
            $eventRejection->setEvent($event);
            $event->addEventRejection($eventRejection);
        }
    }

}