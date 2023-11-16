<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Event\Event;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventNormalizer implements NormalizerInterface
{
    /**
     * @param array<int|string|object|array<int|string|object>> $context
     * @return array<int|string|object|array<int|string|object>>
     */
    public function normalize(mixed $event, string $format = null, array $context = []): array
    {
        return [
            'id' => $event->getId()
                ->toRfc4122(),
            'type' => 'Feature',
            'geometry' => [
                'id' => $event->getId()->toRfc4122(),
                'type' => 'Point',
                'latitude' => $event->getLatitude(),
                'longitude' => $event->getLongitude(),
                'coordinates' => [$event->getLongitude(), $event->getLatitude()],
            ],
            'properties' => [
                'id' => $event->getId()->toRfc4122(),
            ],
        ];
    }

    /**
     * @param array<int|string|object|array<int|string|object>> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Event;
    }
}
