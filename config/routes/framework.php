<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    // Error routes are no longer needed in Symfony 8
    // Error pages can be previewed using the profiler
};
