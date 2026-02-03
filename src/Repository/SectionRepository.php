<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Section>
 */
class SectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /**
     * @return Section[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('s.ordre', 'ASC')
            ->addOrderBy('s.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Section[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.ordre', 'ASC')
            ->addOrderBy('s.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
