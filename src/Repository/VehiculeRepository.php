<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    //    /**
    //     * @return Vehicule[] Returns an array of Vehicule objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vehicule
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function search(string $query): array
{
    return $this->createQueryBuilder('v')
        ->where('v.modele LIKE :query')
        ->orWhere('v.typeVehicule LIKE :query')
        ->orWhere('v.role LIKE :query')
        ->orWhere('v.disponibilite LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->getQuery()
        ->getResult();
}
// src/Repository/VehiculeRepository.php

public function findPinnedPosts(?string $search, string $sort): array
{
    $queryBuilder = $this->createQueryBuilder('v')
        ->where('v.isPinned = :isPinned')
        ->setParameter('isPinned', true);

    // Appliquer le filtre de recherche si un terme est fourni
    if ($search) {
        $queryBuilder->andWhere('v.modele LIKE :search OR v.typeVehicule LIKE :search OR v.role LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    // Appliquer le tri en fonction du critère choisi
    switch ($sort) {
        case 'disponibilite_matin':
            $queryBuilder->andWhere('v.disponibilite = :disponibilite')
                ->setParameter('disponibilite', 'matin');
            break;
        case 'disponibilite_nuit':
            $queryBuilder->andWhere('v.disponibilite = :disponibilite')
                ->setParameter('disponibilite', 'nuit');
            break;
        default:
            // Par défaut, trier par ID
            $queryBuilder->orderBy('v.id', 'ASC');
            break;
    }

    return $queryBuilder->getQuery()->getResult();
}
}
