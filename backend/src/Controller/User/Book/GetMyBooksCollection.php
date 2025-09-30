<?php 

namespace App\Controller\User\Book;

use App\Entity\User;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final class GetMyBooksCollection
{
    /**
     * Retourne la collection de livres de l'utilisateur courant.
     * API Platform utilisera la normalisation via les groupes "book-collection:read" déclarés sur les entités.
     * Ici on renvoie directement l'ArrayIterator afin que la sérialisation prenne en compte les groupes.
     *
     * @return array<int, Book>
     */
    public function __invoke(#[CurrentUser] User $user): array
    {
        return array_values($user->getBooksCollection()->toArray());
    }
}


