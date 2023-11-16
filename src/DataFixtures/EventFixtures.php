<?php

namespace App\DataFixtures;

use App\Factory\Event\EventFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly EventFactory $eventFactory,
        private readonly UserFixtures $userFixtures
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }

    public function getEventData(): array
    {
        return [
            [
                'title' => 'EXAMPLE EVENT',
                ''
            ]
        ];
    }


    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
