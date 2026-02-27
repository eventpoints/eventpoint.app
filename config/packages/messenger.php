<?php

declare(strict_types=1);

use App\Message\OptimizeEventPhotoMessage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                ],
            ],
            'routing' => [
                OptimizeEventPhotoMessage::class => 'async',
            ],
        ],
    ]);
};
