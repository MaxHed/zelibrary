<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findByName(string $name): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOrCreateByName(string $name, ?int $birthYear = null, ?int $deathYear = null): Author
    {
        $author = $this->findByName($name);
        
        if (!$author) {
            $author = new Author();
            $author->setName($name);
            if ($birthYear) {
                $author->setBirthYear($birthYear);
            }
            if ($deathYear) {
                $author->setDeathYear($deathYear);
            }
        }
        
        return $author;
    }
}
