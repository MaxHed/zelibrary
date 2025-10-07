<?php

declare(strict_types=1);

namespace App\State\Book;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\BookRepository;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\State\Pagination\TraversablePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Provider optimisé pour la collection de livres
 * 
 * Évite le problème de requêtes N+1 en chargeant les relations
 * authors et categories via des JOIN dans une seule requête.
 */
final readonly class BooksCollectionProvider implements ProviderInterface
{
    public function __construct(
        private BookRepository $bookRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer les paramètres de pagination et filtres depuis le contexte
        $filters = $context['filters'] ?? [];
        
        // Utiliser la méthode du repository qui fait les JOIN
        $books = $this->bookRepository->findAllWithRelations($filters);
        
        return $books;
    }
}

