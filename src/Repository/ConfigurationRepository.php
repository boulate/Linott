<?php

namespace App\Repository;

use App\Entity\Configuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Configuration>
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    public function findByCle(string $cle): ?Configuration
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.cle = :cle')
            ->setParameter('cle', $cle)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getValue(string $cle, ?string $default = null): ?string
    {
        $config = $this->findByCle($cle);

        return $config ? $config->getValeur() : $default;
    }

    public function setValue(string $cle, ?string $valeur, ?string $description = null): Configuration
    {
        $config = $this->findByCle($cle);

        if (!$config) {
            $config = new Configuration();
            $config->setCle($cle);
        }

        $config->setValeur($valeur);

        if ($description !== null) {
            $config->setDescription($description);
        }

        $em = $this->getEntityManager();
        $em->persist($config);
        $em->flush();

        return $config;
    }
}
