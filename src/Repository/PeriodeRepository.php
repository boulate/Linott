<?php

namespace App\Repository;

use App\Entity\Periode;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Periode>
 */
class PeriodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Periode::class);
    }

    /**
     * @return Periode[]
     */
    public function findByUserAndDate(User $user, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.date = :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('p.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Periode[]
     */
    public function findByUserAndWeek(User $user, \DateTimeInterface $startOfWeek): array
    {
        $endOfWeek = (clone $startOfWeek)->modify('+6 days');

        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.date >= :start')
            ->andWhere('p.date <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startOfWeek->format('Y-m-d'))
            ->setParameter('end', $endOfWeek->format('Y-m-d'))
            ->orderBy('p.date', 'ASC')
            ->addOrderBy('p.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Periode[]
     */
    public function findByUserAndDateRange(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.date >= :start')
            ->andWhere('p.date <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('p.date', 'ASC')
            ->addOrderBy('p.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalMinutesByUserAndDate(User $user, \DateTimeInterface $date): int
    {
        $periodes = $this->findByUserAndDate($user, $date);
        $total = 0;

        foreach ($periodes as $periode) {
            $total += $periode->getDureeMinutes();
        }

        return $total;
    }

    public function getTotalMinutesByUserAndWeek(User $user, \DateTimeInterface $startOfWeek): int
    {
        $periodes = $this->findByUserAndWeek($user, $startOfWeek);
        $total = 0;

        foreach ($periodes as $periode) {
            $total += $periode->getDureeMinutes();
        }

        return $total;
    }

    public function hasOverlap(Periode $periode): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.date = :date')
            ->andWhere('p.heureDebut < :heureFin')
            ->andWhere('p.heureFin > :heureDebut')
            ->setParameter('user', $periode->getUser())
            ->setParameter('date', $periode->getDate()->format('Y-m-d'))
            ->setParameter('heureDebut', $periode->getHeureDebut()->format('H:i:s'))
            ->setParameter('heureFin', $periode->getHeureFin()->format('H:i:s'));

        if ($periode->getId()) {
            $qb->andWhere('p.id != :id')
               ->setParameter('id', $periode->getId());
        }

        $result = $qb->getQuery()->getResult();

        return count($result) > 0;
    }
}
