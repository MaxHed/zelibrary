<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests de la collection personnelle de livres
 * 
 * Vérifie que :
 * - Seuls les utilisateurs authentifiés peuvent gérer leur collection
 * - L'ajout et la suppression fonctionnent correctement
 * - Les endpoints retournent les bons status HTTP
 */
class BookCollectionTest extends WebTestCase
{
    private function authenticate($client): void
    {
        $client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'marie.martin@email.com',
            'password' => 'password',
        ]));
    }

    private function getBookId($client): int
    {
        $container = $client->getContainer();
        $bookRepository = $container->get(BookRepository::class);
        $book = $bookRepository->FindOneBookForTest();
        return $book->getId();
    }

    public function testGetMyCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me/books-collection');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetMyCollectionWhenAuthenticated(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $client->request('GET', '/api/me/books-collection');
        
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testAddBookToCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $bookId = $this->getBookId($client);
        
        $client->request('POST', '/api/me/add-book-to-my-collection/' . $bookId);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAddBookToCollection(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        $bookId = $this->getBookId($client);
        
        $client->request('POST', '/api/me/add-book-to-my-collection/' . $bookId);
        
        $this->assertResponseIsSuccessful();
    }

    public function testAddNonExistentBookToCollection(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        
        $client->request('POST', '/api/me/add-book-to-my-collection/99999');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteBookFromCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $bookId = $this->getBookId($client);
        
        $client->request('DELETE', '/api/me/delete-book-from-my-collection/' . $bookId);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteBookFromCollection(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        $bookId = $this->getBookId($client);
        
        // D'abord ajouter le livre
        $client->request('POST', '/api/me/add-book-to-my-collection/' . $bookId);
        $this->assertResponseIsSuccessful();

        // Puis le supprimer
        $client->request('DELETE', '/api/me/delete-book-from-my-collection/' . $bookId);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCheckIfBookIsInCollection(): void
    {
        $client = static::createClient();
        $this->authenticate($client);
        $bookId = $this->getBookId($client);
        
        // Ajouter un livre
        $client->request('POST', '/api/me/add-book-to-my-collection/' . $bookId);
        $this->assertResponseIsSuccessful();

        // Vérifier qu'il est dans la collection
        $client->request('GET', '/api/me/is-book-in-my-collection/' . $bookId);
        
        $this->assertResponseIsSuccessful();
    }
}

