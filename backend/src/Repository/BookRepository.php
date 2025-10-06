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
}
