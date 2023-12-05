<?php

declare(strict_types=1);

namespace App\Controller\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/social')]
class SocialAuthenticationController extends AbstractController
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry
    ) {
    }

    #[Route(path: '/google/connect', name: 'connect_google_start')]
    public function connectGoogleAuth(): Response
    {
        return $this->clientRegistry
            ->getClient('google')
            ->redirect(
                ['https://www.googleapis.com/auth/userinfo.email ', 'https://www.googleapis.com/auth/userinfo.profile'],
                []
            );
    }

    #[Route(path: '/google/connect/check', name: 'connect_google_check')]
    public function checkGoogleAuth(): void
    {
    }

    #[Route(path: '/facebook/connect', name: 'connect_facebook_start')]
    public function connectFacebookAuth(): Response
    {
        return $this->clientRegistry
            ->getClient('facebook')
            ->redirect(['public_profile', 'email'], []);
    }

    #[Route(path: '/facebook/connect/check', name: 'connect_facebook_check')]
    public function checkFacebookAuth(): void
    {
    }
}
