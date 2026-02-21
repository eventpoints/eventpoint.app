<?php

declare(strict_types=1);

namespace App\Controller\Controller\Ticketing;

use App\Service\Ticketing\WebhookHandler;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{
    public function __construct(
        private readonly WebhookHandler $webhookHandler,
    ) {
    }

    #[Route(path: '/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature', '');

        try {
            $this->webhookHandler->handle($payload, $sigHeader);
            return new Response('', Response::HTTP_OK);
        } catch (SignatureVerificationException) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new Response('Webhook error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
