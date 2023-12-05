<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event\EventRole;
use App\Enum\EventOrganiserRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (EventOrganiserRoleEnum::cases() as $eventOrganiserRoleEnum) {
            $eventRole = new EventRole();
            $eventRole->setTitle($eventOrganiserRoleEnum);
            $manager->persist($eventRole);
        }
        $manager->flush();
    }
}
