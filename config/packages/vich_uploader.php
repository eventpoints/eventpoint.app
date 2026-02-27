<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('vich_uploader', [
        'db_driver' => 'orm',
        'mappings' => [
            'user_avatar' => [
                'uri_prefix' => '/uploads/avatars',
                'upload_destination' => '%kernel.project_dir%/public/uploads/avatars',
                'namer' => SmartUniqueNamer::class,
            ],
            'event_image' => [
                'uri_prefix' => '/uploads/events',
                'upload_destination' => '%kernel.project_dir%/public/uploads/events',
                'namer' => SmartUniqueNamer::class,
            ],
            'event_group_image' => [
                'uri_prefix' => '/uploads/groups',
                'upload_destination' => '%kernel.project_dir%/public/uploads/groups',
                'namer' => SmartUniqueNamer::class,
            ],
            'event_photo' => [
                'uri_prefix' => '/uploads/photos',
                'upload_destination' => '%kernel.project_dir%/public/uploads/photos',
                'namer' => SmartUniqueNamer::class,
            ],
        ],
    ]);
};
