<?php

declare(strict_types = 1);

namespace App\Serializer;

use App\Entity\Asset\Asset;
use App\Entity\Event\Event;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventNormalizer implements NormalizerInterface
{
    /**
     * @param Event $event
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

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Event;
    }
}
