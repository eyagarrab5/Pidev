<?php

namespace App\Repository;

use App\Entity\PropositionCovoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PropositionCovoiturage>
 */
class PropositionCovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropositionCovoiturage::class);
    }

    //    /**
    //     * @return PropositionCovoiturage[] Returns an array of PropositionCovoiturage objects
    //     */
    public function findById($value): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.conducteur_id = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
      public function findByPassager_Id($value): array
       {
        return $this->createQueryBuilder('p')
          ->andWhere('p.passager_id = :val')
               ->setParameter('val', $value)
          ->orderBy('p.id', 'ASC')
            ->getQuery()
             ->getResult()
          ;
       }
       public function findByConducteur_Id($value): array
       {
        return $this->createQueryBuilder('p')
          ->andWhere('p.conducteur_id = :val')
               ->setParameter('val', $value)
          ->orderBy('p.id', 'ASC')
            ->getQuery()
             ->getResult()
          ;
       }

    //    public function findOneBySomeField($value): ?PropositionCovoiturage
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
