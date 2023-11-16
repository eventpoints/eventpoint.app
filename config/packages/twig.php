<?php

declare(strict_types=1);

use App\Service\ApplicationTimeService\ApplicationTimeService;
use App\Service\RegionalSettingsService\RegionalSettingsService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'default_path' => '%kernel.project_dir%/templates',
        'globals' => [
            'regional' => service(RegionalSettingsService::class),
            'supported_locales' => explode(',', $_ENV['SUPPORTED_LOCALES']),
            'app_time' => service(ApplicationTimeService::class),
            'date_pattern' => 'dd.MM.yy',
            'date_time_pattern' => 'dd.MM.yy HH:mm',
            'mapbox_token' => $_ENV['MAPBOX_TOKEN'],
        ],
        'form_themes' => [
            'bootstrap_5_layout.html.twig',
            'form/fields/selection_group.html.twig',
            'form/fields/entity_selection_group.html.twig',
            'form/fields/category_group_type.html.twig',
        ]
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
