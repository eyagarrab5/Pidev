<?php

namespace App\Repository;

use App\Entity\OffreCovoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreCovoiturage>
 */
class OffreCovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreCovoiturage::class);
    }

    //    /**
    //     * @return OffreCovoiturage[] Returns an array of OffreCovoiturage objects
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
    //    public function findOneBySomeField($value): ?OffreCovoiturage
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
