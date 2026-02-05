<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Event\Event;
use App\Service\EventStatusService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EventWorkflowExtension extends AbstractExtension
{
    public function __construct(
        private readonly EventStatusService $eventStatusService,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('event_can', $this->can(...)),
            new TwigFunction('event_available_transitions', $this->getAvailableTransitions(...)),
        ];
    }

    /**
     * Check if a transition can be applied to an event.
     * Usage: event_can(event, 'publish')
     */
    public function can(Event $event, string $transition): bool
    {
        return $this->eventStatusService->can($event, $transition);
    }

    /**
     * Get all available transitions for an event.
     * Usage: event_available_transitions(event)
     *
     * @return array<string>
     */
    public function getAvailableTransitions(Event $event): array
    {
        return $this->eventStatusService->getAvailableTransitions($event);
    }
}
