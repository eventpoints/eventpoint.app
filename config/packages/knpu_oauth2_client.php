<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('knpu_oauth2_client', [
        'clients' => [
            'facebook' => [
                'type' => 'facebook',
                'client_id' => '%env(OAUTH_FACEBOOK_ID)%',
                'client_secret' => '%env(OAUTH_FACEBOOK_SECRET)%',
                'redirect_route' => 'connect_facebook_check',
                'redirect_params' => [],
                'graph_api_version' => 'v18.0',
            ],
            'google' => [
                'type' => 'google',
                'client_id' => '%env(OAUTH_GOOGLE_ID)%',
                'client_secret' => '%env(OAUTH_GOOGLE_SECRET)%',
                'redirect_route' => 'connect_google_check',
                'redirect_params' => [],
            ],
        ],
    ]);
};
