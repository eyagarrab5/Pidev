<?php

namespace App\Repository;

use App\Entity\ForumPosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<ForumPosts>
 */
class ForumPostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPosts::class);
    }
    
    public function getTotalPosts(): int
    {
        return $this->createQueryBuilder('fp')
            ->select('COUNT(fp.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalComments(): int
    {
        return $this->createQueryBuilder('fp')
            ->select('SUM(fp.comments_count)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMostLikedPost(): ?ForumPosts
    {
        return $this->createQueryBuilder('fp')
            ->orderBy('fp.likes', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMostCommentedPost(): ?ForumPosts
    {
        return $this->createQueryBuilder('fp')
            ->orderBy('fp.comments_count', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findBySearchAndSort(?string $search, string $sort, ?string $category = null): array
    {
        $queryBuilder = $this->createQueryBuilder('fp');

        // Ajouter une condition de recherche uniquement si $search n'est pas null
        if ($search !== null) {
            $queryBuilder
                ->andWhere('fp.title LIKE :search OR fp.content LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

         // Filtrer par catégorie si une catégorie est spécifiée
        if ($category !== null) {
            $queryBuilder
                ->andWhere('fp.category = :category')
                ->setParameter('category', $category);
        }

            // Exclure les posts épinglés
        $queryBuilder->andWhere('fp.isPinned = false');

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

    public function findBySort(string $sort): array
    {
        $queryBuilder = $this->createQueryBuilder('fp')
            ->leftJoin('fp.comments', 'c')
            ->addSelect('c');

        // Appliquer le tri en fonction du paramètre
        switch ($sort) {
            case 'newest':
                $queryBuilder->orderBy('fp.createdAt', 'DESC');
                break;
            case 'oldest':
                $queryBuilder->orderBy('fp.createdAt', 'ASC');
                break;
            case 'most_liked':
                $queryBuilder->orderBy('fp.likes', 'DESC');
                break;
            case 'least_liked':
                $queryBuilder->orderBy('fp.likes', 'ASC');
                break;
            default:
                $queryBuilder->orderBy('fp.createdAt', 'DESC');
                break;
        }
        // Log la requête SQL générée
        $sql = $queryBuilder->getQuery()->getSQL();
        dump($sql);
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
    public function findAllWithCommentsQuery(?string $search, string $sort): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c');

        if ($search) {
            $queryBuilder->andWhere('p.title LIKE :search OR p.content LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        switch ($sort) {
            case 'newest':
                $queryBuilder->orderBy('p.createdAt', 'DESC');
                break;
            case 'oldest':
                $queryBuilder->orderBy('p.createdAt', 'ASC');
                break;
            case 'most_liked':
                $queryBuilder->orderBy('p.likes', 'DESC');
                break;
            case 'least_liked':
                $queryBuilder->orderBy('p.likes', 'ASC');
                break;
            default:
                $queryBuilder->orderBy('p.createdAt', 'DESC');
                break;
        }

        return $queryBuilder->getQuery();
    }
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.category = :category')
            ->setParameter('category', $category)
            ->orderBy('fp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPinnedPosts(?string $search, string $sort, ?string $category = null): array
{
    $queryBuilder = $this->createQueryBuilder('fp');

    // Ajouter une condition de recherche uniquement si $search n'est pas null
    if ($search !== null) {
        $queryBuilder
            ->andWhere('fp.title LIKE :search OR fp.content LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    // Filtrer par catégorie si une catégorie est spécifiée
    if ($category !== null) {
        $queryBuilder
            ->andWhere('fp.category = :category')
            ->setParameter('category', $category);
    }

    // Trier uniquement par isPinned en premier
    $queryBuilder->addOrderBy('fp.isPinned', 'DESC');
    // Appliquer le tri en fonction du paramètre
    switch ($sort) {
        case 'oldest':
            $queryBuilder->addOrderBy('fp.createdAt', 'ASC');
            break;
        case 'most_liked':
            $queryBuilder->addOrderBy('fp.likes', 'DESC');
            break;
        case 'least_liked':
            $queryBuilder->addOrderBy('fp.likes', 'ASC');
            break;
        case 'newest':
        default:
            $queryBuilder->addOrderBy('fp.createdAt', 'DESC');
            break;
    }

    return $queryBuilder->getQuery()->getResult();
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