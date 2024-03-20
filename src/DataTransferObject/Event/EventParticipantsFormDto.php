<?php

namespace App\DataTransferObject\Event;

use App\Entity\User\User;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class EventParticipantsFormDto
{
    /**
     * @var ArrayCollection<int, User>
     */
    private ArrayCollection $participants;

    private CarbonImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new CarbonImmutable();
    }

    public function getParticipants(): ArrayCollection
    {
        return $this->participants;
    }

    public function setParticipants(ArrayCollection $participants): void
    {
        $this->participants = $participants;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
