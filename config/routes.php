<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(
        resource: '../src/Controller/Controller/',
        type: 'attribute'
    );

    $routingConfigurator->import(
        resource: '../src/Controller/Admin/',
        type: 'attribute'
    );
};
