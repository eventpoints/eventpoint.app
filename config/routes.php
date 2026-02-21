<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {

    $routingConfigurator->import('@AutocompleteBundle/config/routes.php');

    $routingConfigurator->add('index_redirect', '/')
            ->controller(RedirectController::class)
            ->defaults([
                    'route' => 'events',
                    'permanent' => true,
                    'keepQueryParams' => true,
                    'keepRequestMethod' => true,
            ]);

    $routingConfigurator->import(resource: __DIR__ . '/../src/Controller/Api/', type: 'attribute');

    $routingConfigurator->import(resource: __DIR__ . '/../src/Controller/Controller/', type: 'attribute')
            ->prefix('/{_locale}')
            ->defaults([
                    '_locale' => 'en',
            ])
            ->requirements([
                    '_locale' => '[a-z]{2}',
            ]);


    $routingConfigurator->import(
            resource: '../src/Controller/Admin/',
            type: 'attribute'
    );
};
