<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Book>
 * Repository de l'entité `Book`.
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function FindOneBookForTest(): ?Book
    {
        return $this->createQueryBuilder('b')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Find one book where user did not review
    public function findOneBookNotReviewedBy(User $user): ?Book
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.reviews', 'r', 'WITH', 'r.user = :user')
            ->where('r.id IS NULL')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère tous les livres avec leurs relations authors et categories
     * en une seule requête SQL (évite le problème N+1).
     * 
     * Utilise des LEFT JOIN avec addSelect() pour charger les relations
     * de manière EAGER dans la même requête.
     * 
     * @param array<string, mixed> $filters Filtres optionnels (title, authors.name, categories.name)
     * @return Book[]
     */
    public function findAllWithRelations(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')->addSelect('a')
            ->leftJoin('b.categories', 'c')->addSelect('c')
            ->orderBy('b.createdAt', 'DESC');

        // Appliquer les filtres si présents
        if (isset($filters['title'])) {
            $qb->andWhere('b.title LIKE :title')
               ->setParameter('title', '%' . $filters['title'] . '%');
        }

        if (isset($filters['authors.name'])) {
            $qb->andWhere('a.name LIKE :authorName')
               ->setParameter('authorName', '%' . $filters['authors.name'] . '%');
        }

        if (isset($filters['categories.name'])) {
            $qb->andWhere('c.name LIKE :categoryName')
               ->setParameter('categoryName', '%' . $filters['categories.name'] . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
