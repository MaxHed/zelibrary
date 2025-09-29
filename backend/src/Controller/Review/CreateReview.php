<?php

namespace App\Controller\Review;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Review;
use App\ApiResource\CreateReviewInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

#[AsController]
final class CreateReview
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(#[MapRequestPayload] Review $data, #[CurrentUser] User $user, int $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book instanceof Book) {
            throw new NotFoundHttpException('Book not found');
        }

        // Règle métier: un utilisateur ne peut poster qu'un avis par livre
        $existing = $this->entityManager->getRepository(Review::class)->findOneBy([
            'book' => $book,
            'user' => $user,
        ]);
        if ($existing instanceof Review) {
            throw new ConflictHttpException('You have already reviewed this book');
        }

        $review = $data;
        $review->setBook($book);
        $review->setUser($user);
        $review->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Review created',
        ], Response::HTTP_CREATED);
    }
}


