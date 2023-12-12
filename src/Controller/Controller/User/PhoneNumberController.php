<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Factory\PhoneNumberFactory;
use App\Form\Form\PhoneNumberFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PhoneNumberController extends AbstractController
{
    public function __construct(
        private readonly PhoneNumberFactory $phoneNumberFactory,
        private readonly UserRepository     $userRepository
    ) {
    }

    #[Route(path: '/user/phone-number/create', name: 'create_user_phone_number')]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $phoneNumber = $this->phoneNumberFactory->create(owner: $currentUser);
        $phoneNumberForm = $this->createForm(PhoneNumberFormType::class, $phoneNumber);
        $phoneNumberForm->handleRequest($request);

        if ($phoneNumberForm->isSubmitted() && $phoneNumberForm->isValid()) {
            $currentUser->addPhoneNumber($phoneNumber);
            if ($currentUser->getPhoneNumbers()->count() === 0) {
                $currentUser->setPhoneNumber($phoneNumber);
            }
            $this->userRepository->save($currentUser, true);
            return $this->redirectToRoute('user_account');
        }

        return $this->render('phoneNumber/create.html.twig', [
            'phoneNumberForm' => $phoneNumberForm->createView(),
        ]);
    }
}
