<?php

namespace App\Repository;

use App\Entity\Axe1;
use App\Entity\Axe2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Axe2>
 */
class Axe2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Axe2::class);
    }

    /**
     * @return Axe2[]
     */
    public function findByAxe1(Axe1 $axe1, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.axe1 = :axe1')
            ->setParameter('axe1', $axe1)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe2[]
     */
    public function findByAxe1Id(int $axe1Id, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.axe1 = :axe1Id')
            ->setParameter('axe1Id', $axe1Id)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe2[]
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

    /**
     * Find the default "_IND_" axe2 for orphan axes
     */
    public function findIndependentDefault(): ?Axe2
    {
        return $this->findOneBy(['code' => '_IND_']);
    }
}
