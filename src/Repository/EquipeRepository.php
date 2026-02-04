<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    /**
     * @return Equipe[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Equipe[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les équipes avec le nombre d'utilisateurs.
     *
     * @return array
     */
    public function findAllWithUserCount(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e', 'COUNT(u.id) as userCount')
            ->leftJoin('e.users', 'u')
            ->groupBy('e.id')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
