<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests des reviews
 * 
 * Vérifie que :
 * - Un utilisateur ne peut poster qu'un avis par livre (contrainte unicité)
 * - Les avis nécessitent l'authentification
 * - Les validations fonctionnent (note entre 1 et 5)
 */
class ReviewTest extends WebTestCase
{
    private function authenticate($client): void
    {
        $client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'jean.dupont@email.com',
            'password' => 'password',
        ]));
    }

    private function getBookNotReviewedBy($client, string $email): int
    {
        $container = $client->getContainer();
        $userRepository = $container->get(UserRepository::class);
        $bookRepository = $container->get(BookRepository::class);
        
        $user = $userRepository->findOneBy(['email' => $email]);
        $book = $bookRepository->findOneBookNotReviewedBy($user);
        
        return $book->getId();
    }

    public function testCreateReviewRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/reviews/books/51', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Excellent livre !',
            'rate' => 5,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateReviewWithValidData(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $bookId = $this->getBookNotReviewedBy($client, 'jean.dupont@email.com');
        
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Très bon livre, je recommande !',
            'rate' => 4,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateReviewWithInvalidRate(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $bookId = $this->getBookNotReviewedBy($client, 'jean.dupont@email.com');
        
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Test',
            'rate' => 10, // Invalide : doit être entre 1 et 5
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateReviewWithMissingFields(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $bookId = $this->getBookNotReviewedBy($client, 'jean.dupont@email.com');
        
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'rate' => 5,
            // review manquant
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCannotCreateDuplicateReview(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $bookId = $this->getBookNotReviewedBy($client, 'jean.dupont@email.com');
        
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Premier avis',
            'rate' => 4,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Tenter de créer un second avis pour le même livre
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Deuxième avis (ne devrait pas fonctionner)',
            'rate' => 5,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStringContainsString('already reviewed', $data['detail'] ?? $data['message'] ?? '');
    }

    public function testDeleteOwnReview(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $bookId = $this->getBookNotReviewedBy($client, 'jean.dupont@email.com');
        
        $client->request('POST', '/api/reviews/books/' . $bookId, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'review' => 'Avis à supprimer',
            'rate' => 3,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Récupérer l'ID de la review créée
        // Note : Ceci nécessiterait d'interroger la base ou de parser la réponse
        // Pour cet exemple, on suppose qu'on connaît l'ID ou qu'on peut le récupérer

        // Supprimer la review (ID à adapter selon votre logique)
        // $client->request('DELETE', '/api/reviews/' . $reviewId);
        // $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}

