<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

use App\Entity\Ticketing\Order;
use App\Entity\Ticketing\StripeWebhookEvent;
use App\Entity\Ticketing\Ticket;
use App\Enum\OrderStatusEnum;
use App\Enum\TicketStatusEnum;
use App\Repository\Ticketing\OrderRepository;
use App\Repository\Ticketing\StripeWebhookEventRepository;
use App\Repository\Ticketing\TicketRepository;
use App\Service\MixpanelService;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

final class WebhookHandler
{
    public function __construct(
        private readonly string $stripeSecretKey,
        private readonly string $stripeWebhookSecret,
        private readonly OrderRepository $orderRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly StripeWebhookEventRepository $webhookEventRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MixpanelService $mixpanel,
    ) {
    }

    public function handle(string $payload, string $sigHeader): void
    {
        Stripe::setApiKey($this->stripeSecretKey);

        $event = Webhook::constructEvent($payload, $sigHeader, $this->stripeWebhookSecret);

        // Idempotency check
        $existing = $this->webhookEventRepository->findByStripeEventId($event->id);
        if ($existing !== null && $existing->getStatus() === 'processed') {
            return;
        }

        $webhookEvent = $existing ?? new StripeWebhookEvent($event->id, $event->type);
        $this->webhookEventRepository->save($webhookEvent);

        try {
            match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event),
                'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event),
                'charge.refunded' => $this->handleChargeRefunded($event),
                default => null,
            };

            $webhookEvent->setStatus('processed');
            $webhookEvent->setProcessedAt(CarbonImmutable::now());
        } catch (\Throwable $e) {
            $webhookEvent->setStatus('failed');
            throw $e;
        } finally {
            $this->entityManager->flush();
        }
    }

    private function handleCheckoutSessionCompleted(Event $event): void
    {
        $session = $event->data->object;
        $orderId = $session->metadata->order_id ?? null;

        if ($orderId === null) {
            return;
        }

        $order = $this->orderRepository->find($orderId);
        if ($order === null || $order->getStatus() !== OrderStatusEnum::PENDING) {
            return;
        }

        $order->setStatus(OrderStatusEnum::PAID);
        $order->setStripePaymentIntentId($session->payment_intent);

        $this->mixpanel->trackOrderCompleted($order->getBuyer(), $order);

        foreach ($order->getOrderLines() as $line) {
            for ($i = 0; $i < $line->getQuantity(); $i++) {
                $ticket = new Ticket($line);
                $this->entityManager->persist($ticket);
            }
        }
    }

    private function handlePaymentIntentFailed(Event $event): void
    {
        $paymentIntent = $event->data->object;

        $order = $this->orderRepository->findOneBy([
            'stripePaymentIntentId' => $paymentIntent->id,
        ]);

        if ($order === null) {
            return;
        }

        $order->setStatus(OrderStatusEnum::FAILED);
    }

    private function handleChargeRefunded(Event $event): void
    {
        $charge = $event->data->object;

        $order = $this->orderRepository->findOneBy([
            'stripeChargeId' => $charge->id,
        ]);

        if ($order === null) {
            $order = $this->orderRepository->findOneBy([
                'stripePaymentIntentId' => $charge->payment_intent,
            ]);
        }

        if ($order === null) {
            return;
        }

        $order->setStatus(OrderStatusEnum::REFUNDED);
        $order->setStripeChargeId($charge->id);

        $this->mixpanel->trackOrderRefunded($order->getBuyer(), $order);

        foreach ($order->getOrderLines() as $line) {
            $tickets = $this->ticketRepository->findBy(['orderLine' => $line]);
            foreach ($tickets as $ticket) {
                $ticket->setStatus(TicketStatusEnum::REFUNDED);
            }
        }
    }
}
