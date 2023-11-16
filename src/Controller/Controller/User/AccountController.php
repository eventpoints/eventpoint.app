<?php

namespace App\Controller\Controller\User;

use App\Entity\User;
use App\Enum\FlashEnum;
use App\Form\Form\UserAccountFormType;
use App\Repository\UserRepository;
use App\Service\ImageUploadService\ImageUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user')]
class AccountController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ImageUploadService         $imageUploadService,
        private readonly TranslatorInterface         $translator,
    ) {
    }
    #[Route(path: '/account', name: 'user_account', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $userAccountForm = $this->createForm(UserAccountFormType::class, $currentUser);
        $userAccountForm->handleRequest($request);
        if ($userAccountForm->isSubmitted() && $userAccountForm->isValid()) {
            $avatarData = $userAccountForm->get('avatar')->getData();
            if (! empty($avatarData)) {
                $avatar = $this->imageUploadService->processAvatar($avatarData);
                $currentUser->setAvatar($avatar->getEncoded());
            }

            $plainPassword = $userAccountForm->get('password')->getData();
            if (! empty($plainPassword)) {
                $hashedPassword = $this->hasher->hashPassword($currentUser, $plainPassword);
                $currentUser->setPassword($hashedPassword);
            }

            $this->userRepository->save($currentUser, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'userAccountForm' => $userAccountForm,
        ]);
    }
}