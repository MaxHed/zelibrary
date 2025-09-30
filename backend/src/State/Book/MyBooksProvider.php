<?php

namespace App\State\Book;

use App\Entity\User;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

final class MyBooksProvider implements ProviderInterface
{
    public function __construct(private Security $security) {}

    public function provide(?object $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous n\'êtes pas authentifié');
        }

        if (!$user instanceof User) {
            return [];
        }
        return $user->getBooksCollection()->toArray();
    }
}
