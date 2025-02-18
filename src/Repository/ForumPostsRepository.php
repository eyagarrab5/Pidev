<?php

namespace App\Repository;

use App\Entity\ForumPosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumPosts>
 */
class ForumPostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPosts::class);
    }

    public function findBySearchAndSort(?string $search, string $sort): array
    {
        $queryBuilder = $this->createQueryBuilder('fp');

        // Ajouter une condition de recherche uniquement si $search n'est pas null
        if ($search !== null) {
            $queryBuilder
                ->andWhere('fp.title LIKE :search OR fp.content LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Appliquer le tri en fonction du paramètre
        switch ($sort) {
            case 'oldest':
                $queryBuilder->orderBy('fp.createdAt', 'ASC');
                break;
            case 'most_liked':
                $queryBuilder->orderBy('fp.likes', 'DESC');
                break;
            case 'least_liked':
                $queryBuilder->orderBy('fp.likes', 'ASC');
                break;
            case 'newest':
            default:
                $queryBuilder->orderBy('fp.createdAt', 'DESC');
                break;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllWithComments(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ForumPosts[] Returns an array of ForumPosts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ForumPosts
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
