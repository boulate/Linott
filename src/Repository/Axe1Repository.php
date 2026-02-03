<?php

namespace App\Repository;

use App\Entity\Axe1;
use App\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Axe1>
 */
class Axe1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Axe1::class);
    }

    /**
     * @return Axe1[]
     */
    public function findBySection(Section $section, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $section)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe1[]
     */
    public function findBySectionId(int $sectionId, bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :sectionId')
            ->setParameter('sectionId', $sectionId)
            ->orderBy('a.ordre', 'ASC')
            ->addOrderBy('a.libelle', 'ASC');

        if ($activeOnly) {
            $qb->andWhere('a.actif = :actif')
               ->setParameter('actif', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Axe1[]
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
