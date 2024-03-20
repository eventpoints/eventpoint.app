<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Data\Cities;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CityRepository $cityRepository,
        private readonly Cities $cities,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $countries = $this->countryRepository->findAll();
        foreach ($countries as $country) {
            $cities = $this->cities->getCountryCities(alpha2: $country->getAlpha2());

            if (count($this->cityRepository->findBy([
                'country' => $country,
            ])) === count($cities)) {
                continue;
            }

            foreach ($cities as $cityData) {
                $city = new City(
                    name: strtolower((string) $cityData['name']),
                    latitude: $cityData['latitude'],
                    longitude: $cityData['longitude'],
                    country: $country
                );

                if ($cityData['capital']) {
                    $country->setCapitalCity($city);
                }

                $country->addCity($city);
            }

            $manager->persist($country);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }
}
