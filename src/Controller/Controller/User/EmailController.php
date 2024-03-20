<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\EmailFactory;
use App\Form\Form\User\DefaultEmailFormType;
use App\Form\Form\User\EmailAddressFormType;
use App\Repository\User\EmailRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly EmailFactory $emailFactory,
        private readonly EmailRepository $emailRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route(path: '/user/emails', name: 'user_email_addresses', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function manageEmailAddress(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $defaultEmailForm = $this->createForm(DefaultEmailFormType::class);
        $defaultEmailForm->handleRequest($request);

        if ($defaultEmailForm->isSubmitted() && $defaultEmailForm->isValid()) {
            $defaultEmail = $defaultEmailForm->get('email')->getData();
            $currentUser->setEmail($defaultEmail);
            $this->userRepository->save($currentUser, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('user_email_addresses');
        }

        return $this->render('user/emails.html.twig', [
            'defaultEmailForm' => $defaultEmailForm,
        ]);
    }

    //    #[Route(path: '/user/email/{id}', name: 'show_user_email_address', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    //    public function show(Email $email, Request $request, #[CurrentUser] User $currentUser): Response
    //    {
    //        return $this->render('user/email.html.twig');
    //    }
    //
    #[Route(path: '/user/email/create', name: 'create_user_email', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $emailForm = $this->createForm(EmailAddressFormType::class);
        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $emailAddress = $emailForm->get('address')->getData();
            $email = $this->emailRepository->findOneBy([
                'address' => $emailAddress,
            ]);

            if ($email instanceof Email) {
                if ($email->getOwner() === $currentUser) {
                    $this->addFlash(FlashEnum::MESSAGE->value, 'email address already on your account');
                } else {
                    $this->addFlash(FlashEnum::MESSAGE->value, 'an account already exists with that email address. if you still wish to add this email address to this account, close the other account first.');
                }
                return $this->redirectToRoute('create_user_email');
            }
            $email = $this->emailFactory->create(emailAddress: $emailAddress, user: $currentUser);

            $currentUser->addEmail($email);
            $this->entityManager->persist($email);
            $this->entityManager->flush();
            return $this->redirectToRoute('user_email_addresses');
        }

        return $this->render('user/email/create.html.twig', [
            'emailForm' => $emailForm,
        ]);
    }
}
