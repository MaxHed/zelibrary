<?php
declare(strict_types=1);

namespace App\Controller\Review;

use App\Entity\User;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}


