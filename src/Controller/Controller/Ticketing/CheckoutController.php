<?php

declare(strict_types=1);

namespace App\Controller\Controller\Ticketing;

use App\Entity\Embeddable\Money;
use App\Entity\Event\Event;
use App\Entity\Ticketing\Order;
use App\Entity\Ticketing\OrderLine;
use App\Form\Form\Ticketing\CheckoutForm;
use App\Repository\Ticketing\OrderRepository;
use App\Service\Ticketing\FeeCalculator;
use App\Service\Ticketing\StripeCheckoutService;
use App\Service\Ticketing\TicketMerchantGate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly FeeCalculator $feeCalculator,
        private readonly StripeCheckoutService $stripeCheckoutService,
        private readonly OrderRepository $orderRepository,
        private readonly TicketMerchantGate $merchantGate,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(path: '/events/{id}/checkout', name: 'event_checkout', methods: ['GET', 'POST'])]
    public function checkout(Event $event, Request $request): Response
    {
        $ticketOptions = $event->getTicketOptions()->filter(
            fn ($option) => $option->isEnabled() && !$option->getPrice()->isZero()
        )->toArray();

        if (empty($ticketOptions)) {
            $this->addFlash('warning', $this->translator->trans('ticketing.checkout.no_tickets_available'));
            return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
        }

        $form = $this->createForm(CheckoutForm::class, null, ['ticket_options' => $ticketOptions]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User\User $user */
            $user = $this->getUser();

            $currency = $ticketOptions[array_key_first($ticketOptions)]->getPrice()->getCurrency();
            $order = new Order($event, $user, $currency);
            $totalCents = 0;

            foreach ($ticketOptions as $ticketOption) {
                $fieldName = 'ticket_' . $ticketOption->getId()->toRfc4122();
                $qty = (int) $form->get($fieldName)->getData();

                if ($qty <= 0) {
                    continue;
                }

                $unitPriceCents = $ticketOption->getPrice()->getAmount() ?? 0;
                $unitPrice = new Money($unitPriceCents, $ticketOption->getPrice()->getCurrency());
                $line = new OrderLine($order, $ticketOption, $qty, $unitPrice);
                $order->addOrderLine($line);
                $totalCents += $qty * $unitPriceCents;
            }

            if ($totalCents === 0) {
                $this->addFlash('warning', $this->translator->trans('ticketing.checkout.select_at_least_one'));
                return $this->redirectToRoute('event_checkout', ['id' => $event->getId()]);
            }

            $feeCents = $this->feeCalculator->calculate($totalCents);
            $order->getTotal()->setAmount($totalCents);
            $order->getPlatformFee()->setAmount($feeCents);

            $this->orderRepository->save($order, true);

            $successUrl = $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

            $session = $this->stripeCheckoutService->createSession($order, $successUrl, $cancelUrl);

            return $this->redirect($session->url);
        }

        return $this->render('ticketing/checkout.html.twig', [
            'event' => $event,
            'form' => $form,
            'ticketOptions' => $ticketOptions,
        ]);
    }

    #[Route(path: '/checkout/success', name: 'checkout_success', methods: ['GET'])]
    public function success(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');

        $order = null;
        if ($sessionId !== null) {
            $order = $this->orderRepository->findOneBy(['stripeCheckoutSessionId' => $sessionId]);
        }

        return $this->render('ticketing/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route(path: '/checkout/cancel', name: 'checkout_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        $this->addFlash('info', $this->translator->trans('ticketing.checkout.cancelled'));
        return $this->redirectToRoute('events');
    }
}
