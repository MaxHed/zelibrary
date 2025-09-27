<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findByNameAndType(string $name, string $type): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :name')
            ->andWhere('c.type = :type')
            ->setParameter('name', $name)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOrCreateByNameAndType(string $name, string $type): Category
    {
        $category = $this->findByNameAndType($name, $type);
        
        if (!$category) {
            $category = new Category();
            $category->setName($name);
            $category->setType($type);
        }
        
        return $category;
    }
}
