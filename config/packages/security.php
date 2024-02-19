<?php

declare(strict_types=1);

use App\Entity\User;
use App\Security\CustomAuthenticator;
use App\Security\FacebookAuthenticator;
use App\Security\GoogleAuthenticator;
use App\Security\User\UserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'enable_authenticator_manager' => true,
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'users' => [
                'id' => UserProvider::class,
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'users',
                'entry_point' => CustomAuthenticator::class,
                'custom_authenticators' => [
                    CustomAuthenticator::class,
                    FacebookAuthenticator::class,
                    GoogleAuthenticator::class,
                ],
                'logout' => [
                    'path' => 'app_logout',
                ],
            ],
        ],
        'access_control' => null,
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
