<?php

namespace App\Data;

class Cities
{
    /**
     * @return array<string, array<mixed>>
     */
    public function getCountryCities(string $alpha2): array
    {
        return match ($alpha2) {
            'CZ' => $this->getCzechCities(),
            'DE' => $this->getGermanCities(),
            default => []
        };
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function getCzechCities(): array
    {
        return [
            [
                'name' => 'Prague',
                'latitude' => 50.0755,
                'longitude' => 14.4378,
                'country_code' => 'CZ',
                'capital' => true,
            ],
            [
                'name' => 'Brno',
                'latitude' => 49.1951,
                'longitude' => 16.6068,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Ostrava',
                'latitude' => 49.8209,
                'longitude' => 18.2625,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Plzeň',
                'latitude' => 49.7384,
                'longitude' => 13.3736,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Liberec',
                'latitude' => 50.7671,
                'longitude' => 15.0565,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Olomouc',
                'latitude' => 49.5938,
                'longitude' => 17.2508,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'České Budějovice',
                'latitude' => 48.9745,
                'longitude' => 14.4744,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Hradec Králové',
                'latitude' => 50.2093,
                'longitude' => 15.8323,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Ústí nad Labem',
                'latitude' => 50.6607,
                'longitude' => 14.0328,
                'country_code' => 'CZ',
                'capital' => false,
            ],
            [
                'name' => 'Pardubice',
                'latitude' => 50.0379,
                'longitude' => 15.7815,
                'country_code' => 'CZ',
                'capital' => false,
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function getGermanCities(): array
    {
        return [
            [
                'name' => 'Berlin',
                'latitude' => 52.5200,
                'longitude' => 13.4050,
                'country_code' => 'DE',
                'capital' => true,
            ],
            [
                'name' => 'Hamburg',
                'latitude' => 53.5511,
                'longitude' => 9.9937,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Munich',
                'latitude' => 48.1351,
                'longitude' => 11.5820,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Cologne',
                'latitude' => 50.9375,
                'longitude' => 6.9603,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Frankfurt',
                'latitude' => 50.1109,
                'longitude' => 8.6821,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Stuttgart',
                'latitude' => 48.7758,
                'longitude' => 9.1829,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Düsseldorf',
                'latitude' => 51.2277,
                'longitude' => 6.7735,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Dortmund',
                'latitude' => 51.5136,
                'longitude' => 7.4653,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Essen',
                'latitude' => 51.4556,
                'longitude' => 7.0116,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Leipzig',
                'latitude' => 51.3397,
                'longitude' => 12.3731,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Bremen',
                'latitude' => 53.0793,
                'longitude' => 8.8017,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Hanover',
                'latitude' => 52.3759,
                'longitude' => 9.7320,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Dresden',
                'latitude' => 51.0504,
                'longitude' => 13.7373,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Nuremberg',
                'latitude' => 49.4521,
                'longitude' => 11.0767,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Duisburg',
                'latitude' => 51.4344,
                'longitude' => 6.7623,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Bochum',
                'latitude' => 51.4818,
                'longitude' => 7.2194,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Wuppertal',
                'latitude' => 51.2562,
                'longitude' => 7.1507,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Bielefeld',
                'latitude' => 52.0302,
                'longitude' => 8.5325,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Bonn',
                'latitude' => 50.7374,
                'longitude' => 7.0982,
                'country_code' => 'DE',
                'capital' => false,
            ],
            [
                'name' => 'Mannheim',
                'latitude' => 49.4875,
                'longitude' => 8.4660,
                'country_code' => 'DE',
                'capital' => false,
            ],
        ];
    }
}
