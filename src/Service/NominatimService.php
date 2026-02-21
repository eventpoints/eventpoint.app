<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class NominatimService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getCityBoundary(string $cityName, string $countryName): ?array
    {
        $cacheKey = 'nominatim_boundary_' . md5($cityName . '_' . $countryName);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($cityName, $countryName): ?array {
            $item->expiresAfter(86400 * 30);

            $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $cityName . ', ' . $countryName,
                    'format' => 'json',
                    'polygon_geojson' => 1,
                    'limit' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'EventPoint/1.0',
                ],
            ]);

            $data = $response->toArray();

            if (empty($data)) {
                return null;
            }

            $result = $data[0];

            if (!isset($result['geojson'])) {
                return null;
            }

            return [
                'geojson' => $result['geojson'],
                'display_name' => $result['display_name'] ?? $cityName,
            ];
        });
    }
}
