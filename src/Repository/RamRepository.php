<?php

namespace App\Repository;

use App\Entity\Ram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\Traits\RepositoryTrait;

/**
 * @method Ram|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ram|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ram[]    findAll()
 * @method Ram[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RamRepository extends ServiceEntityRepository
{
    use RepositoryTrait;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ram::class);
    }
}
