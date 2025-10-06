<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[AsController]
class UserController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private Security $security
    ) {}

    #[Route('/api/me', name: 'api_user_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        $books = [];
        foreach ($user->getBooksCollection() as $book) {
            $books[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ];
        }

        $reviews = [];
        foreach ($user->getReviews() as $review) {
            $reviews[] = [
                'id' => $review->getId(),
                'rate' => $review->getRate(),
                'createdAt' => $review->getCreatedAt()?->format(DATE_ATOM),
            ];
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'employedAt' => $user->getEmployedAt()?->getId(),
            'booksCollection' => $books,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/api/register', name: 'api_user_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email et mot de passe requis'], 400);
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Un utilisateur avec cet email existe déjà'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'message' => 'Utilisateur créé avec succès'
        ], 201);
    }

    /**
     * ENDPOINT RETIRÉ : Faille de sécurité critique (reset password sans validation).
     * Pour implémenter un vrai reset password, voir PasswordResetController.
     */
}
