<?php

namespace App\Repository;

use App\Entity\Axe2;
use App\Entity\Axe3;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Axe3>
 */
class Axe3Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Axe3::class);
    }

    /**
     * @return Axe3[]
     */
    public function findByAxe2(Axe2 $axe2, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.axe2 = :axe2')
            ->setParameter('axe2', $axe2)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe3[]
     */
    public function findByAxe2Id(int $axe2Id, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.axe2 = :axe2Id')
            ->setParameter('axe2Id', $axe2Id)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe3[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
