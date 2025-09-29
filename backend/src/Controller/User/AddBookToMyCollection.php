<?php 

namespace App\Controller\User;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
final class AddBookToMyCollection
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

        $user->addBooksCollection($bookEntity);
        $this->entityManager->flush();


        return new Response('Book added to my collection', Response::HTTP_OK);
    }
}