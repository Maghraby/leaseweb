<?php

namespace App\DataFixtures;

use App\Entity\Server;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServerFixtures extends Fixture
{
    public const SERVER_REFERENCE = 'server';
    public const SERVER_REFERENCE2 = 'server2';

    public function load(ObjectManager $manager)
    {
        $server = new Server();
        $server->setBrand('Dell');
        $server->setName('R210');
        $server->setAssetId(1232423);
        $server->SetPrice(12344);
        $server->setCreatedAt(new \DateTime);
        $server->setUpdatedAt(new \DateTime);

        $manager->persist($server);
        $manager->flush();


        $server2 = new Server();
        $server2->setBrand('HP');
        $server2->setName('R730');
        $server2->setAssetId(12322423);
        $server2->SetPrice(12344);
        $server2->setCreatedAt(new \DateTime);
        $server2->setUpdatedAt(new \DateTime);

        $manager->persist($server2);
        $manager->flush();

        $this->addReference(self::SERVER_REFERENCE, $server);
        $this->addReference(self::SERVER_REFERENCE2, $server2);
    }
}
