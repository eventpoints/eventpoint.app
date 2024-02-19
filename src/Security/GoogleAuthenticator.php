<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\SocialAuth;
use App\Entity\User;
use App\Factory\EmailFactory;
use App\Factory\SocialAuthFactory;
use App\Factory\UserFactory;
use App\Repository\EmailRepository;
use App\Repository\SocialAuthRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private const OAUTH_PROVIDER = 'GOOGLE_AUTH_PROVIDER';

    private const ROUTE = 'connect_google_check';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly SocialAuthRepository $socialAuthRepository,
        private readonly UserRepository $userRepository,
        private readonly RouterInterface $router,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserFactory $userFactory,
        private readonly SocialAuthFactory $socialAuthFactory,
        private readonly EmailFactory $emailFactory,
        private readonly EmailRepository $emailRepository
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === self::ROUTE;
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $emailAddress = $googleUser->getEmail();

                // 1) have they logged in with Google before? Easy!
                $socialAuth = $this->socialAuthRepository->findOneBy([
                    'token' => $googleUser->getId(),
                ]);

                if ($socialAuth instanceof SocialAuth) {
                    return $socialAuth->getOwner();
                }

                // 2) do we have a matching user by email?
                $user = $this->emailRepository->findOneBy([
                    'address' => $emailAddress,
                ])?->getOwner();

                if (! $user instanceof User) {
                    // 3) Maybe you just want to "register" them
                    $email = $this->emailFactory->create(emailAddress:  $emailAddress,user: $user);

                    $user = $this->userFactory->create(
                        firstName: $googleUser->getFirstName() . '',
                        lastName: $googleUser->getLastName() . '',
                        email: $email,
                        password: null,
                        avatar: $googleUser->getAvatar(),
                    );
                    $user->addEmail($email);
                    $user->setEmail($email);

                    $socialAuth = $this->socialAuthFactory->create(
                        token: $googleUser->getId(),
                        provider: self::OAUTH_PROVIDER,
                        owner: $user
                    );

                    $this->entityManager->persist($socialAuth);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('user_event_invitations');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $loginRoute = $this->router->generate('app_login');

        return new RedirectResponse($loginRoute);
    }
}
