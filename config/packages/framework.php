<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'secret' => '%env(APP_SECRET)%',
        'http_method_override' => false,
        'handle_all_throwables' => true,
        'session' => [
            'handler_id' => null,
            'cookie_secure' => 'auto',
            'cookie_samesite' => 'lax',
            'storage_factory_id' => 'session.storage.factory.native',
        ],
        'http_client' => [
            'scoped_clients' => [
                'cloudflare.turnstile.client' => [
                    'base_uri' => 'https://challenges.cloudflare.com',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ],
                'mapbox.client' => [
                    'base_uri' => 'https://api.mapbox.com',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ],
            ],
        ],
        'php_errors' => [
            'log' => true,
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('framework', [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);
    }
};
