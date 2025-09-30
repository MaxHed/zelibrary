<?php 

namespace App\Controller\User\Book;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final class IsBookInMyCollection
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(#[CurrentUser] User $user, int $book): Response
    {
        $bookEntity = $this->entityManager->getRepository(Book::class)->find($book);
        if (!$bookEntity instanceof Book) {
            throw new NotFoundHttpException('Book not found');
        }

        $inCollection = $user->getBooksCollection()->contains($bookEntity);

        return new JsonResponse(['inCollection' => $inCollection], Response::HTTP_OK);
    }
}


