<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\EmailFactory;
use App\Form\Form\User\RegistrationFormType;
use App\Repository\User\EmailRepository;
use App\Repository\User\UserRepository;
use App\Security\CustomAuthenticator;
use App\Security\EmailVerifier;
use App\Service\AvatarService\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly EmailVerifier $emailVerifier,
        private readonly HttpClientInterface $cloudflareTurnstileClient,
        private readonly TranslatorInterface $translator,
        private readonly EmailFactory $emailFactory,
        private readonly EmailRepository $emailRepository,
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, CustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($_ENV['APP_ENV'] === 'prod') {
                $response = $this->cloudflareTurnstileClient->request(Request::METHOD_POST, '/turnstile/v0/siteverify', [
                    'body' => [
                        'secret' => $this->getParameter('CLOUDFLARE_TURNSTILE_PRIVATE_KEY'),
                        'response' => $request->request->get('cf-turnstile-response'),
                        'ip' => $request->getClientIp(),
                    ],
                ]);

                $isCaptchaSuccessful = json_decode($response->getContent())->success;

                if (! $isCaptchaSuccessful) {
                    $this->addFlash(FlashEnum::MESSAGE->value, $this->translator->trans('something-went-wrong'));
                    return $this->redirectToRoute('app_register');
                }
            }

            $emailAddress = $form->get('email')->getData();
            $email = $this->emailRepository->findOneBy([
                'address' => $emailAddress,
            ]);

            if (! $email instanceof Email) {
                $email = $this->emailFactory->create(emailAddress: $emailAddress, user: $user);
                $user->setEmail($email);
                $email->setOwner($user);
                $entityManager->persist($email);
            } else {
                if ($email->getOwner() instanceof User) {
                    $form->addError(new FormError(Email::DUPLICATE_EMAIL_ADDRESS));
                } else {
                    $user->setEmail($email);
                    $email->setOwner($user);
                }
            }

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $avatar = $this->avatarService->createAvatar($user->getEmail()->getAddress());
            $user->setAvatar($avatar);

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if ($id === null) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if ($user === null) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
