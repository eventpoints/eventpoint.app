<?php

declare(strict_types=1);

use App\Model\BrowserRegionalData;
use App\Model\RegionalConfiguration;
use App\Service\ApplicationTimeService\ApplicationTimeService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'default_path' => '%kernel.project_dir%/templates',
        'globals' => [
            'app_name' => 'Eventpoint',
            'regional' => service(RegionalConfiguration::class),
            'browser_timezone' => service(BrowserRegionalData::class),
            'supported_locales' => explode(',', (string) $_ENV['SUPPORTED_LOCALES']),
            'app_time' => service(ApplicationTimeService::class),
            'date_pattern' => 'dd MM yy',
            'date_time_pattern' => 'dd MM yy HH:mm',
            'time_pattern' => 'HH:mm',
            'short_day_date_pattern' => 'E, dd MMM yy',
            'day_date_pattern' => 'E, dd MMM yyyy',
            'mapbox_token' => '%env(MAPBOX_TOKEN)%',
            'turnstile_public_key' => '%env(CLOUDFLARE_TURNSTILE_PUBLIC_KEY)%',
        ],
        'form_themes' => [
            'bootstrap_5_layout.html.twig',
            'form/fields/selection_group.html.twig',
            'form/fields/entity_selection_group.html.twig',
            'form/fields/category_group_type.html.twig',
            'form/fields/custom_enum_group.html.twig',
            'form/fields/custom_checkbox.html.twig',
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
