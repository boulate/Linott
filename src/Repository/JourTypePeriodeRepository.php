<?php

namespace App\Repository;

use App\Entity\JourTypePeriode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JourTypePeriode>
 */
class JourTypePeriodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourTypePeriode::class);
    }
}
