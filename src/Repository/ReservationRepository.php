<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
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
    public function findByPassager_Id(int $passagerId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.passager_id = :passagerId')
            ->setParameter('passagerId', $passagerId)
            ->getQuery()
            ->getResult();
    }
    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
