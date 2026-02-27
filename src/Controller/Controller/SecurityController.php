<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\DataTransferObject\PasswordResetDto;
use App\Entity\User\User;
use App\Entity\User\UserToken;
use App\Enum\UserTokenPurposeEnum;
use App\Form\Form\PasswordFormType;
use App\Form\Form\PasswordResetFormType;
use App\Repository\User\EmailRepository;
use App\Service\EmailService\EmailService;
use App\Service\MixpanelService;
use App\Service\UserTokenService\UserTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
        private readonly EmailRepository $emailRepository,
        private readonly EmailService $emailService,
        private readonly UserTokenService $userTokenService,
        private readonly MixpanelService $mixpanel,
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/password-reset', name: 'request_password_reset')]
    public function passwordReset(Request $request): Response
    {
        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordResetDto $dto */
            $dto = $form->getData();

            $emailEntity = $this->emailRepository->findOneBy(['address' => $dto->getEmail()]);
            $user = $emailEntity?->getOwner();

            $this->mixpanel->trackPasswordResetRequested();

            // Always show success to prevent user enumeration
            if ($user instanceof User) {
                $token = $this->userTokenService->issueToken(
                    user: $user,
                    purpose: UserTokenPurposeEnum::PASSWORD_RESET,
                );

                $this->emailService->sendPasswordResetEmail(email: $emailEntity, context: [
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            $this->addFlash('success', $this->translator->trans('password.reset-email-sent'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password-reset.html.twig', [
            'passwordResetForm' => $form,
        ]);
    }

    #[Route('/user/set-password/{token}', name: 'user_set_password')]
    public function setPassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        #[MapEntity(mapping: ['token' => 'value'])]
        ?UserToken $userToken = null,
    ): Response {
        if (
            !$userToken instanceof UserToken
            || !$userToken->isActive()
            || $userToken->getPurpose() !== UserTokenPurposeEnum::PASSWORD_RESET
        ) {
            $this->addFlash('error', $this->translator->trans('password.reset-link-invalid'));
            return $this->redirectToRoute('app_login');
        }

        $user = $userToken->getOwner();

        $form = $this->createForm(PasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = (string) $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plain));

            $this->userTokenService->consumeAllForUserAndPurpose(
                user: $user,
                purpose: UserTokenPurposeEnum::PASSWORD_RESET,
            );

            $this->entityManager->flush();

            $this->mixpanel->trackPasswordResetCompleted($user);
            $this->addFlash('success', $this->translator->trans('password.changed-success'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/set-password.html.twig', [
            'passwordForm' => $form,
        ]);
    }
}
