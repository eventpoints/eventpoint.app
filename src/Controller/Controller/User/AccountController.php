<?php

declare(strict_types=1);

namespace App\Controller\Controller\User;

use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Form\Form\User\ChangeUserPasswordFormType;
use App\Form\Form\User\DefaultPhoneNumberFormType;
use App\Form\Form\User\UserAccountFormType;
use App\Form\Form\User\UserPasswordFormType;
use App\Repository\User\UserRepository;
use App\Service\ImageUploadService\ImageService;
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
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ImageService $imageUploadService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(path: '/account', name: 'user_account', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $userAccountForm = $this->createForm(UserAccountFormType::class, $currentUser);
        $userAccountForm->handleRequest($request);
        if ($userAccountForm->isSubmitted() && $userAccountForm->isValid()) {
            $avatarData = $userAccountForm->get('avatar')->getData();

            if (! empty($avatarData)) {
                $avatar = $this->imageUploadService->processAvatar($avatarData);
                $currentUser->setAvatar($avatar->getEncoded());
            }

            $this->userRepository->save($currentUser, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changes-saved'));
            return $this->redirectToRoute('user_account');
        }

        $phoneNumberForm = $this->createForm(DefaultPhoneNumberFormType::class);

        $phoneNumberForm->handleRequest($request);
        if ($phoneNumberForm->isSubmitted() && $phoneNumberForm->isValid()) {
            $phoneNumber = $phoneNumberForm->get('phoneNumber')->getData();
            $currentUser->setPhoneNumber($phoneNumber);
            $this->userRepository->save($currentUser, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('phone-number-added'));
            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'userAccountForm' => $userAccountForm,
            'phoneNumberForm' => $phoneNumberForm,
        ]);
    }

    #[Route(path: '/reset-password', name: 'reset_user_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function setPassword(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $userPasswordForm = $this->createForm(UserPasswordFormType::class, $currentUser);
        $userPasswordForm->handleRequest($request);
        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            $plainPassword = $userPasswordForm->get('password')->getData();
            if (! empty($plainPassword)) {
                $hashedPassword = $this->hasher->hashPassword($currentUser, $plainPassword);
                $currentUser->setPassword($hashedPassword);
            }

            $this->userRepository->save($currentUser, true);
            $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changed-saved'));
            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/reset-password.html.twig', [
            'userPasswordForm' => $userPasswordForm,
        ]);
    }

    #[Route(path: '/change-password', name: 'change_user_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function changePassword(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $userPasswordForm = $this->createForm(ChangeUserPasswordFormType::class, $currentUser);
        $userPasswordForm->handleRequest($request);

        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            $plainCurrentPassword = $userPasswordForm->get('currentPassword')->getData();
            $plainNewPassword = $userPasswordForm->get('newPassword')->getData();

            if ($this->hasher->isPasswordValid($currentUser, $plainCurrentPassword)) {
                if (! empty($plainNewPassword)) {
                    $hashedPassword = $this->hasher->hashPassword($currentUser, $plainNewPassword);
                    $currentUser->setPassword($hashedPassword);
                    $this->userRepository->save($currentUser, true);
                    $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('changed-saved'));
                }
            } else {
                $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('old-password-incorrect'));
            }

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/change-password.html.twig', [
            'userPasswordForm' => $userPasswordForm,
        ]);
    }
}
