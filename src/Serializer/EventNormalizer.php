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
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'type' => 'Feature',
            'geometry' => [
                'id' => $object->getId()->toRfc4122(),
                'type' => 'Point',
                'latitude' => $object->getLatitude(),
                'longitude' => $object->getLongitude(),
                'coordinates' => [$object->getLongitude(), $object->getLatitude()],
            ],
            'properties' => [
                'id' => $object->getId()->toRfc4122(),
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

    public function getSupportedTypes(?string $format): array
    {
        return [
            Event::class => true,
        ];
    }
}
