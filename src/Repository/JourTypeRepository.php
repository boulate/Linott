<?php

namespace App\Repository;

use App\Entity\JourType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JourType>
 */
class JourTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourType::class);
    }

    /**
     * Retourne les modèles personnels de l'utilisateur + les modèles partagés actifs.
     *
     * @return JourType[]
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('jt')
            ->where('jt.user = :user')
            ->orWhere('jt.partage = true AND jt.actif = true')
            ->setParameter('user', $user)
            ->orderBy('jt.ordre', 'ASC')
            ->addOrderBy('jt.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne uniquement les modèles personnels de l'utilisateur.
     *
     * @return JourType[]
     */
    public function findPersonnelsByUser(User $user): array
    {
        return $this->createQueryBuilder('jt')
            ->where('jt.user = :user')
            ->setParameter('user', $user)
            ->orderBy('jt.ordre', 'ASC')
            ->addOrderBy('jt.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne uniquement les modèles partagés (globaux).
     *
     * @return JourType[]
     */
    public function findPartages(bool $activeOnly = true): array
    {
        $qb = $this->createQueryBuilder('jt')
            ->where('jt.partage = true')
            ->andWhere('jt.user IS NULL');

        if ($activeOnly) {
            $qb->andWhere('jt.actif = true');
        }

        return $qb
            ->orderBy('jt.ordre', 'ASC')
            ->addOrderBy('jt.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne tous les modèles partagés pour l'admin.
     *
     * @return JourType[]
     */
    public function findAllPartages(): array
    {
        return $this->findPartages(false);
    }
}
