<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\EventGroup\EventGroupRole;
use App\Enum\EventGroupRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventGroupRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (EventGroupRoleEnum::cases() as $eventGroupRoleEnum) {
            $eventRole = new EventGroupRole();
            $eventRole->setTitle($eventGroupRoleEnum);
            $manager->persist($eventRole);
        }
        $manager->flush();
    }
}
