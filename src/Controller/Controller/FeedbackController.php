<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\Feedback;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Form\Form\FeedbackFormType;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/feedback')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly FeedbackRepository $feedbackRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/new', name: 'create_feedback')]
    public function new(Request $request): Response
    {
        $feedback = new Feedback();
        $currentUser = $this->getUser();

        if ($currentUser instanceof User) {
            $feedback->setOwner($currentUser);
        }

        $feedbackForm = $this->createForm(FeedbackFormType::class, $feedback);
        $feedbackForm->handleRequest($request);

        if ($feedbackForm->isSubmitted() && $feedbackForm->isValid()) {
            $this->feedbackRepository->save($feedbackForm->getData(), true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('feedback-given'));

            return $this->redirectToRoute('events');
        }

        return $this->render('feedback/new.html.twig', [
            'feedbackForm' => $feedbackForm->createView(),
        ]);
    }
}
