<?php

declare(strict_types=1);

namespace App\Controller\Controller\Ticketing;

use App\Entity\Ticketing\Order;
use App\Service\Ticketing\RefundService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class RefundController extends AbstractController
{
    public function __construct(
        private readonly RefundService $refundService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(path: '/orders/{id}/refund', name: 'order_refund', methods: ['POST'])]
    public function refund(Order $order): Response
    {
        $event = $order->getEvent();

        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        if (! $event->getIsOrganiser($user)) {
            throw $this->createAccessDeniedException('Only organisers can issue refunds.');
        }

        try {
            $this->refundService->refund($order);
            $this->addFlash('success', $this->translator->trans('ticketing.refund.success'));
        } catch (\LogicException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('order_detail', [
            'id' => $order->getId(),
        ]);
    }

    #[Route(path: '/orders/{id}', name: 'order_detail', methods: ['GET'])]
    public function detail(Order $order): Response
    {
        /** @var \App\Entity\User\User $user */
        $user = $this->getUser();

        if ($order->getBuyer() !== $user && ! $order->getEvent()->getIsOrganiser($user)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('ticketing/order-detail.html.twig', [
            'order' => $order,
        ]);
    }
}
