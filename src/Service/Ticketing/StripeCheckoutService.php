<?php

declare(strict_types=1);

namespace App\Service\Ticketing;

use App\Entity\Ticketing\Order;
use App\Repository\Ticketing\OrderRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;

final class StripeCheckoutService
{
    public function __construct(
        private readonly string $stripeSecretKey,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    public function createSession(Order $order, string $successUrl, string $cancelUrl): Session
    {
        Stripe::setApiKey($this->stripeSecretKey);

        $stripeAccountId = null;
        foreach ($order->getEvent()->getOrganisers() as $participant) {
            $profile = $participant->getOwner()->getTicketMerchantProfile();
            if ($profile?->getStripeAccountId() !== null) {
                $stripeAccountId = $profile->getStripeAccountId();
                break;
            }
        }

        $lineItems = [];
        foreach ($order->getOrderLines() as $line) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($line->getUnitPrice()->getCurrency()),
                    'product_data' => [
                        'name' => $line->getTicketOption()->getTitle(),
                    ],
                    'unit_amount' => $line->getUnitPrice()->getAmount(),
                ],
                'quantity' => $line->getQuantity(),
            ];
        }

        $params = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'order_id' => (string) $order->getId(),
            ],
        ];

        if ($order->getPlatformFee()->getAmount() > 0 && $stripeAccountId !== null) {
            $params['payment_intent_data'] = [
                'application_fee_amount' => $order->getPlatformFee()->getAmount(),
            ];
        }

        $options = [];
        if ($stripeAccountId !== null) {
            $options['stripe_account'] = $stripeAccountId;
        }

        $session = Session::create($params, $options ?: null);

        $order->setStripeCheckoutSessionId($session->id);
        $this->orderRepository->save($order, true);

        return $session;
    }
}
