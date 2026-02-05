<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Enum\FlashEnum;
use App\Factory\EmailFactory;
use App\Form\Form\User\RegistrationFormType;
use App\Repository\User\EmailRepository;
use App\Security\CustomAuthenticator;
use App\Service\AvatarService\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly AvatarService $avatarService,
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

}
