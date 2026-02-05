<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final readonly class EventStatusService
{
    public function __construct(
        private WorkflowInterface $eventStatusStateMachine,
    ) {
    }

    public function can(Event $event, string $transition): bool
    {
        return $this->eventStatusStateMachine->can($event, $transition);
    }

    public function apply(Event $event, string $transition): void
    {
        $this->eventStatusStateMachine->apply($event, $transition);
    }

    /**
     * @return array<string>
     */
    public function getAvailableTransitions(Event $event): array
    {
        return array_map(
            static fn ($t) => $t->getName(),
            $this->eventStatusStateMachine->getEnabledTransitions($event)
        );
    }
}
