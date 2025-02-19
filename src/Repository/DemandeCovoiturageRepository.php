<?php

namespace App\Repository;

use App\Entity\DemandeCovoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeCovoiturage>
 */
class DemandeCovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeCovoiturage::class);
    }

    //    /**
    //     * @return DemandeCovoiturage[] Returns an array of DemandeCovoiturage objects
    //     */
        public function findById($value): array
        {
            return $this->createQueryBuilder('d')
                ->andWhere('d.passager_id = :val')
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
    //    public function findOneBySomeField($value): ?DemandeCovoiturage
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
