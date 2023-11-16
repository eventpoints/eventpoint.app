<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import([
        'path' => '../src/Controller/Controller',
        'namespace' => 'App\Controller\Controller',
    ], 'attribute');

    $routingConfigurator->import('../src/Controller/Admin', 'annotation');
};
