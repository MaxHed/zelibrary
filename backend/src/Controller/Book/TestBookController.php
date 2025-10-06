<?php

namespace App\Controller\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/api/test/books', name: 'api_book_test', methods: ['GET'])]
final class TestBookController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly Security $security
    )
    {
    }

    public function __invoke(): JsonResponse
    {
        try {
            // If env is not dev, return 404
            $isDev = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
            if (!$isDev) {
                return new JsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
            }
            $user = $this->security->getUser();
            if (!$user instanceof User) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
    
            $book = $this->bookRepository->findOneBookNotReviewedBy($user);
            dd($book);
            return new JsonResponse($book);
            
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

