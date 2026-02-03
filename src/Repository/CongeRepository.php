<?php

namespace App\Repository;

use App\Entity\Conge;
use App\Entity\TypeConge;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conge>
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    /**
     * @return Conge[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Conge[]
     */
    public function findByUserAndYear(User $user, int $year): array
    {
        $startDate = new \DateTimeImmutable("$year-01-01");
        $endDate = new \DateTimeImmutable("$year-12-31");

        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.dateDebut >= :start')
            ->andWhere('c.dateDebut <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('c.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Conge[]
     */
    public function findByUserAndMonth(User $user, int $year, int $month): array
    {
        $startDate = new \DateTimeImmutable("$year-$month-01");
        $endDate = $startDate->modify('last day of this month');

        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('(c.dateDebut <= :end AND c.dateFin >= :start)')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('c.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countJoursByUserAndTypeAndYear(User $user, TypeConge $type, int $year): float
    {
        $startDate = new \DateTimeImmutable("$year-01-01");
        $endDate = new \DateTimeImmutable("$year-12-31");

        $result = $this->createQueryBuilder('c')
            ->select('SUM(c.nbJours) as total')
            ->andWhere('c.user = :user')
            ->andWhere('c.type = :type')
            ->andWhere('c.statut = :statut')
            ->andWhere('c.dateDebut >= :startDate')
            ->andWhere('c.dateDebut <= :endDate')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->setParameter('statut', Conge::STATUT_VALIDE)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * @return Conge[]
     */
    public function findPendingByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', Conge::STATUT_EN_ATTENTE)
            ->orderBy('c.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Conge[]
     */
    public function findAllPending(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.statut = :statut')
            ->setParameter('statut', Conge::STATUT_EN_ATTENTE)
            ->join('c.user', 'u')
            ->orderBy('c.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
