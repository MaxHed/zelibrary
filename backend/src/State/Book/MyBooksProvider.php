<?php

declare(strict_types=1);

namespace App\State\Book;

use App\Entity\User;
use App\Repository\UserRepository;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

/**
 * Provider optimisé pour la collection de livres de l'utilisateur
 * 
 * Charge les livres avec leurs relations (authors, categories)
 * en une seule requête pour éviter le problème N+1.
 */
final readonly class MyBooksProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
    ) {}

    public function provide(?object $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous n\'êtes pas authentifié');
        }

        if (!$user instanceof User) {
            return [];
        }

        // Récupérer l'utilisateur avec ses livres et les relations chargées (EAGER)
        $userWithBooks = $this->userRepository->findOneWithBooksAndRelations($user->getId());
        
        if (!$userWithBooks) {
            return [];
        }

        return $userWithBooks->getBooksCollection()->toArray();
    }
}
