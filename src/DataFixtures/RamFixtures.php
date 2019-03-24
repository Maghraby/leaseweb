<?php

namespace App\DataFixtures;

use App\Entity\Ram;
use App\DataFixtures\ServerFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RamFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $server = $this->getReference(ServerFixtures::SERVER_REFERENCE);
        $server2 = $this->getReference(ServerFixtures::SERVER_REFERENCE2);

        $ram = new Ram();
        $ram->setServer($server);
        $ram->setType('DDR');
        $ram->setSize(2);

        $manager->persist($ram);

        $ram2 = new Ram();
        $ram2->setServer($server);
        $ram2->setType('DDR');
        $ram2->setSize(3);

        $manager->persist($ram2);

        $ram3 = new Ram();
        $ram3->setServer($server2);
        $ram3->setType('DDR2');
        $ram3->setSize(3);

        $manager->persist($ram3);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ServerFixtures::class,
        );
    }
}
