<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

use App\Entity\Ticketing\Order;
use App\Enum\OrderStatusEnum;
use App\Enum\TicketStatusEnum;
use App\Repository\Ticketing\OrderRepository;
use App\Repository\Ticketing\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Refund;
use Stripe\Stripe;

final class RefundService
{
    public function __construct(
        private readonly string $stripeSecretKey,
        private readonly OrderRepository $orderRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function refund(Order $order): void
    {
        if ($order->getStatus() !== OrderStatusEnum::PAID) {
            throw new \LogicException(sprintf('Cannot refund order in status "%s".', $order->getStatus()->value));
        }

        if ($order->getStripeChargeId() === null && $order->getStripePaymentIntentId() === null) {
            throw new \LogicException('Order has no Stripe charge or payment intent to refund.');
        }

        Stripe::setApiKey($this->stripeSecretKey);

        $params = ['refund_application_fee' => true];
        if ($order->getStripeChargeId() !== null) {
            $params['charge'] = $order->getStripeChargeId();
        } else {
            $params['payment_intent'] = $order->getStripePaymentIntentId();
        }

        Refund::create($params);

        $order->setStatus(OrderStatusEnum::REFUNDED);

        foreach ($order->getOrderLines() as $line) {
            $tickets = $this->ticketRepository->findBy(['orderLine' => $line]);
            foreach ($tickets as $ticket) {
                $ticket->setStatus(TicketStatusEnum::REFUNDED);
            }
        }

        $this->entityManager->flush();
    }
}
