<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event\Event;
use App\Entity\Event\EventInvitation;
use App\Entity\EventGroup\EventGroup;
use App\Entity\EventGroup\EventGroupDiscussion;
use App\Entity\Ticketing\Order;
use App\Entity\User\User;
use Mixpanel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class MixpanelService
{
    private ?Mixpanel $mixpanel = null;

    public function __construct(
        #[Autowire('%env(MIXPANEL_PROJECT_TOKEN)%')]
        private readonly string $projectToken,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
    ) {
        if ($this->projectToken !== '' && $this->projectToken !== '0') {
            $this->mixpanel = Mixpanel::getInstance($this->projectToken, [
                'host' => 'api-eu.mixpanel.com',
                'use_ssl' => true,
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Auth & Account
    // -------------------------------------------------------------------------

    /**
     * Track user signup.
     *
     * @param array<string, mixed> $properties
     */
    public function trackSignUp(User $user, string $userType, string $registrationMethod, array $properties = []): void
    {
        $this->track('Sign Up', $user, array_merge([
            'user_type' => $userType,
            'registration_method' => $registrationMethod,
        ], $properties));

        $this->setUserProfile($user, $userType);
    }

    public function trackLogin(User $user): void
    {
        $this->track('Login', $user);
    }

    public function trackPasswordResetRequested(): void
    {
        $this->track('Password Reset Requested');
    }

    public function trackPasswordResetCompleted(User $user): void
    {
        $this->track('Password Reset Completed', $user);
    }

    public function trackProfileUpdated(User $user): void
    {
        $this->track('Profile Updated', $user);
    }

    public function trackPasswordChanged(User $user): void
    {
        $this->track('Password Changed', $user);
    }

    // -------------------------------------------------------------------------
    // Events
    // -------------------------------------------------------------------------

    /**
     * Track event posting created.
     *
     * @param array<string, mixed> $properties
     */
    public function trackEventPosted(User $user, array $properties = []): void
    {
        $this->track('Event Posted', $user, $properties);
    }

    public function trackEventCreated(User $user, Event $event): void
    {
        $this->track('Event Created', $user, $this->eventProperties($event));
    }

    public function trackEventUpdated(User $user, Event $event): void
    {
        $this->track('Event Updated', $user, $this->eventProperties($event));
    }

    public function trackEventCancelled(User $user, Event $event): void
    {
        $this->track('Event Cancelled', $user, $this->eventProperties($event));
    }

    public function trackEventRSVPRequested(User $user, Event $event): void
    {
        $this->track('Event RSVP Requested', $user, $this->eventProperties($event));
    }

    public function trackEventRSVPCancelled(User $user, Event $event): void
    {
        $this->track('Event RSVP Cancelled', $user, $this->eventProperties($event));
    }

    public function trackInvitationAccepted(User $user, EventInvitation $invitation): void
    {
        $this->track('Invitation Accepted', $user, array_merge(
            $this->eventProperties($invitation->getEvent()),
            ['invitation_id' => $invitation->getId()?->toString()]
        ));
    }

    public function trackInvitationDeclined(User $user, EventInvitation $invitation): void
    {
        $this->track('Invitation Declined', $user, array_merge(
            $this->eventProperties($invitation->getEvent()),
            ['invitation_id' => $invitation->getId()?->toString()]
        ));
    }

    // -------------------------------------------------------------------------
    // Ticketing
    // -------------------------------------------------------------------------

    public function trackCheckoutStarted(User $user, Order $order): void
    {
        $this->track('Checkout Started', $user, $this->orderProperties($order));
    }

    public function trackOrderCompleted(User $user, Order $order): void
    {
        $this->track('Order Completed', $user, $this->orderProperties($order));
    }

    public function trackOrderRefunded(User $user, Order $order): void
    {
        $this->track('Order Refunded', $user, $this->orderProperties($order));
    }

    // -------------------------------------------------------------------------
    // Groups
    // -------------------------------------------------------------------------

    public function trackGroupCreated(User $user, EventGroup $group): void
    {
        $this->track('Group Created', $user, $this->groupProperties($group));
    }

    public function trackGroupJoinRequested(User $user, EventGroup $group): void
    {
        $this->track('Group Join Requested', $user, $this->groupProperties($group));
    }

    public function trackGroupJoined(User $user, EventGroup $group): void
    {
        $this->track('Group Joined', $user, $this->groupProperties($group));
    }

    public function trackGroupLeft(User $user, EventGroup $group): void
    {
        $this->track('Group Left', $user, $this->groupProperties($group));
    }

    public function trackGroupJoinRequestCancelled(User $user, EventGroup $group): void
    {
        $this->track('Group Join Request Cancelled', $user, $this->groupProperties($group));
    }

    public function trackDiscussionCreated(User $user, EventGroupDiscussion $discussion): void
    {
        $this->track('Discussion Created', $user, [
            'discussion_id' => $discussion->getId()?->toString(),
            'group_id' => $discussion->getEventGroup()?->getId()?->toString(),
            'group_name' => $discussion->getEventGroup()?->getName(),
        ]);
    }

    public function trackDiscussionCommentCreated(User $user, EventGroupDiscussion $discussion): void
    {
        $this->track('Discussion Comment Created', $user, [
            'discussion_id' => $discussion->getId()?->toString(),
            'group_id' => $discussion->getEventGroup()?->getId()?->toString(),
            'group_name' => $discussion->getEventGroup()?->getName(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Track an event.
     *
     * @param array<string, mixed> $properties
     */
    private function track(string $eventName, ?User $user = null, array $properties = []): void
    {
        if (! $this->mixpanel instanceof \Mixpanel) {
            $this->logger->debug('Mixpanel: Project token not configured, skipping event.', [
                'event_name' => $eventName,
            ]);
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        $defaultProperties = [];

        if ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            $defaultProperties['$ip'] = $request->getClientIp();
            $defaultProperties['$browser'] = $request->headers->get('User-Agent');
            $defaultProperties['locale'] = $request->getLocale();
        }

        $mergedProperties = array_merge($defaultProperties, $properties);

        $distinctId = $user?->getId()?->toString() ?? ($request?->getSession()?->getId() ?? uniqid('anon_', true));

        try {
            $this->mixpanel->track($eventName, array_merge(
                ['distinct_id' => $distinctId],
                $mergedProperties
            ));

            $this->logger->info('Mixpanel: Event tracked successfully.', [
                'event_name' => $eventName,
                'distinct_id' => $distinctId,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Mixpanel: Failed to track event.', [
                'event_name' => $eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set or update user profile in Mixpanel.
     */
    private function setUserProfile(User $user, string $userType): void
    {
        if (! $this->mixpanel instanceof \Mixpanel) {
            return;
        }

        $distinctId = $user->getId()->toString();

        try {
            $this->mixpanel->people->set($distinctId, [
                '$email' => $user->getEmail()?->getAddress(),
                '$first_name' => $user->getFirstName(),
                '$last_name' => $user->getLastName(),
                '$created' => $user->getCreatedAt()->format('Y-m-d\TH:i:s'),
                'user_type' => $userType,
            ]);

            $this->logger->info('Mixpanel: User profile set.', [
                'distinct_id' => $distinctId,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Mixpanel: Failed to set user profile.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Identify a user (alias anonymous ID to user ID).
     */
    public function identify(User $user, ?string $anonymousId = null): void
    {
        if (! $this->mixpanel instanceof \Mixpanel) {
            return;
        }

        $userId = $user->getId()->toString();

        if ($anonymousId !== null) {
            try {
                $this->mixpanel->createAlias($userId, $anonymousId);
            } catch (\Throwable $e) {
                $this->logger->error('Mixpanel: Failed to create alias.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function eventProperties(Event $event): array
    {
        return [
            'event_id' => $event->getId()?->toString(),
            'event_title' => $event->getTitle(),
            'event_start_at' => $event->getStartAt()?->toIso8601String(),
            'event_is_private' => $event->isIsPrivate(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function orderProperties(Order $order): array
    {
        return [
            'order_id' => $order->getId()->toString(),
            'event_id' => $order->getEvent()->getId()?->toString(),
            'event_title' => $order->getEvent()->getTitle(),
            'total_amount' => $order->getTotal()->getAmount(),
            'currency' => $order->getTotal()->getCurrency(),
            'order_status' => $order->getStatus()->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function groupProperties(EventGroup $group): array
    {
        return [
            'group_id' => $group->getId()->toString(),
            'group_name' => $group->getName(),
            'group_is_private' => $group->getIsPrivate(),
            'group_country' => $group->getCountry(),
        ];
    }
}
