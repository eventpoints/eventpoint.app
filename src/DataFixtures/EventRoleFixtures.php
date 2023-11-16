<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event\EventRole;
use App\Enum\EventRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAllCategories() as $name => $title) {
            $eventRole = new EventRole();
            $eventRole->setTitle($title);
            $eventRole->setName($name);
            $manager->persist($eventRole);
            $manager->flush();
        }
    }

    public
    function getAllCategories(): array
    {
        return EventRoleEnum::getEventRoles();
    }
}
