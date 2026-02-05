<?php

declare(strict_types=1);

use App\Entity\Event\Event;
use App\Enum\EventStatusEnum;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            'event_status' => [
                'type' => 'state_machine',
                'audit_trail' => [
                    'enabled' => true,
                ],
                'marking_store' => [
                    'type' => 'method',
                    'property' => 'status',
                ],
                'supports' => [Event::class],
                'initial_marking' => EventStatusEnum::DRAFT->value,
                'places' => [
                    EventStatusEnum::DRAFT->value,
                    EventStatusEnum::AWAITING_REVIEW->value,
                    EventStatusEnum::PUBLISHED->value,
                    EventStatusEnum::ACCEPTING_ADMISSIONS->value,
                    EventStatusEnum::ADMISSIONS_CLOSED->value,
                    EventStatusEnum::CANCELLED->value,
                ],
                'transitions' => [
                    'submit_for_review' => [
                        'from' => EventStatusEnum::DRAFT->value,
                        'to' => EventStatusEnum::AWAITING_REVIEW->value,
                    ],
                    'approve' => [
                        'from' => EventStatusEnum::AWAITING_REVIEW->value,
                        'to' => EventStatusEnum::PUBLISHED->value,
                    ],
                    'reject' => [
                        'from' => EventStatusEnum::AWAITING_REVIEW->value,
                        'to' => EventStatusEnum::DRAFT->value,
                    ],
                    'publish' => [
                        'from' => EventStatusEnum::DRAFT->value,
                        'to' => EventStatusEnum::PUBLISHED->value,
                    ],
                    'open_admissions' => [
                        'from' => EventStatusEnum::PUBLISHED->value,
                        'to' => EventStatusEnum::ACCEPTING_ADMISSIONS->value,
                    ],
                    'close_admissions' => [
                        'from' => EventStatusEnum::ACCEPTING_ADMISSIONS->value,
                        'to' => EventStatusEnum::ADMISSIONS_CLOSED->value,
                    ],
                    'reopen_admissions' => [
                        'from' => EventStatusEnum::ADMISSIONS_CLOSED->value,
                        'to' => EventStatusEnum::ACCEPTING_ADMISSIONS->value,
                    ],
                    'cancel' => [
                        'from' => [
                            EventStatusEnum::DRAFT->value,
                            EventStatusEnum::AWAITING_REVIEW->value,
                            EventStatusEnum::PUBLISHED->value,
                            EventStatusEnum::ACCEPTING_ADMISSIONS->value,
                            EventStatusEnum::ADMISSIONS_CLOSED->value,
                        ],
                        'to' => EventStatusEnum::CANCELLED->value,
                    ],
                ],
            ],
        ],
    ]);
};
