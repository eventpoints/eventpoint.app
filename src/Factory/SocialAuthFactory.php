<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SocialAuth;
use App\Entity\User;

class SocialAuthFactory
{
    public function create(null|string $token, string $provider, null|User $owner): SocialAuth
    {
        $socialAuth = new SocialAuth();
        $socialAuth->setToken($token);
        $socialAuth->setProvider($provider);
        $socialAuth->setOwner($owner);

        return $socialAuth;
    }
}
