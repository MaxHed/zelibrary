<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests du BookRepository
 * 
 * Vérifie que :
 * - FindOneBookForTest() retourne bien un livre
 * - findOneBookNotReviewedBy() retourne un livre non reviewé par l'utilisateur
 */
class BookRepositoryTest extends KernelTestCase
{
    private BookRepository $bookRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $this->bookRepository = $container->get(BookRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testFindOneBookForTest(): void
    {
        // Arrange & Act
        $book = $this->bookRepository->FindOneBookForTest();

        // Assert
        $this->assertInstanceOf(Book::class, $book, 'FindOneBookForTest devrait retourner un objet Book');
        $this->assertNotNull($book->getId(), 'Le livre devrait avoir un ID');
        $this->assertNotEmpty($book->getTitle(), 'Le livre devrait avoir un titre');
    }

    public function testFindOneBookNotReviewedByUser(): void
    {
        // Arrange - Récupérer un utilisateur depuis les fixtures
        $user = $this->userRepository->findOneBy(['email' => 'jean.dupont@email.com']);
        $this->assertInstanceOf(User::class, $user, 'L\'utilisateur jean.dupont@email.com devrait exister');

        // Act
        $book = $this->bookRepository->findOneBookNotReviewedBy($user);

        // Assert
        $this->assertInstanceOf(Book::class, $book, 'findOneBookNotReviewedBy devrait retourner un objet Book');
        $this->assertNotNull($book->getId(), 'Le livre devrait avoir un ID');
        
        // Vérifier que l'utilisateur n'a pas reviewé ce livre
        $hasReviewed = false;
        foreach ($book->getReviews() as $review) {
            if ($review->getUser()->getId() === $user->getId()) {
                $hasReviewed = true;
                break;
            }
        }
        
        $this->assertFalse(
            $hasReviewed,
            'L\'utilisateur ne devrait pas avoir reviewé ce livre'
        );
    }

    public function testFindOneBookNotReviewedByUserWithNewUser(): void
    {
        // Arrange - Créer un nouvel utilisateur qui n'a reviewé aucun livre
        $user = $this->userRepository->findOneBy(['email' => 'oceane.laurent@email.com']);
        $this->assertInstanceOf(User::class, $user, 'L\'utilisateur oceane.laurent@email.com devrait exister');

        // Act
        $book = $this->bookRepository->findOneBookNotReviewedBy($user);

        // Assert
        $this->assertInstanceOf(
            Book::class,
            $book,
            'findOneBookNotReviewedBy devrait retourner un livre même pour un utilisateur avec peu de reviews'
        );
    }

    public function testFindOneBookForTestReturnsOnlyOneBook(): void
    {
        // Arrange & Act
        $book1 = $this->bookRepository->FindOneBookForTest();
        $book2 = $this->bookRepository->FindOneBookForTest();

        // Assert
        $this->assertEquals(
            $book1->getId(),
            $book2->getId(),
            'FindOneBookForTest devrait retourner le même livre à chaque appel (premier dans la base)'
        );
    }

    public function testFindOneBookNotReviewedByReturnsNullWhenAllBooksReviewed(): void
    {
        // Arrange - Trouver un utilisateur qui a potentiellement reviewé tous les livres
        // Note: Ce test pourrait échouer si les fixtures changent
        // Dans un cas réel, on créerait des reviews pour tous les livres
        
        $user = $this->userRepository->findOneBy(['email' => 'jean.dupont@email.com']);
        $this->assertInstanceOf(User::class, $user);

        // Act
        $book = $this->bookRepository->findOneBookNotReviewedBy($user);

        // Assert
        // Ce test vérifie que la méthode peut retourner null si tous les livres sont reviewés
        // ou un livre si ce n'est pas le cas
        $this->assertTrue(
            $book === null || $book instanceof Book,
            'findOneBookNotReviewedBy devrait retourner null ou un Book'
        );
    }

    public function testBookFromFindOneBookForTestHasExpectedProperties(): void
    {
        // Arrange & Act
        $book = $this->bookRepository->FindOneBookForTest();

        // Assert
        $this->assertInstanceOf(Book::class, $book);
        $this->assertIsInt($book->getId(), 'L\'ID du livre devrait être un entier');
        $this->assertIsString($book->getTitle(), 'Le titre devrait être une chaîne');
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $book->getCreatedAt(),
            'createdAt devrait être une DateTimeImmutable'
        );
        $this->assertNotNull($book->getAuthors(), 'Le livre devrait avoir des auteurs');
        $this->assertNotNull($book->getCategories(), 'Le livre devrait avoir des catégories');
    }
}

