<?php

namespace App\Controller\Review;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Review;
use App\ApiResource\CreateReviewInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsController]
final class DeleteReview
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(#[CurrentUser] User $user, Review $review): Response
    {
        $userReview = $review->getUser();
        if ($userReview !== $user) {
            throw new AccessDeniedHttpException('Vous n\'êtes pas le propriétaire de cet avis');
        }

        $this->entityManager->remove($review);

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Review deleted',
        ], Response::HTTP_CREATED);
    }
}


