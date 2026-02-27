<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(Mixpanel::class, Mixpanel::class)
        ->factory([
            Mixpanel::class,
            'getInstance',
        ])
        ->args([
            '%env(MIXPANEL_PROJECT_TOKEN)%',
        ]);
};
