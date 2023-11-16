<?php

namespace App\DataFixtures;

use App\Entity\EventGroup\EventGroupRole;
use App\Enum\EventGroupRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventGroupRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAllCategories() as $name => $title) {
            $eventRole = new EventGroupRole();
            $eventRole->setTitle($title);
            $eventRole->setName($name);
            $manager->persist($eventRole);
            $manager->flush();
        }
    }

    public
    function getAllCategories(): array
    {
        return EventGroupRoleEnum::getGroupRoles();
    }
}
