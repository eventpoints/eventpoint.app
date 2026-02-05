<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User\Email;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserFactory $userFactory,
    ) {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $author = $this->userFactory->create(
            firstName: 'Event',
            lastName: 'Author',
            email: (new Email())->setAddress('author@example.com'),
            password: '12345678'
        );
        $manager->persist($author);
        $this->addReference('user-author', $author);

        $guest = $this->userFactory->create(
            firstName: 'Event',
            lastName: 'Guest',
            email: (new Email())->setAddress('guest@example.com'),
            password: '12345678'
        );
        $manager->persist($guest);
        $this->addReference('user-guest', $guest);

        $manager->flush();
    }
}
