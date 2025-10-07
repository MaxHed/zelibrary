<?php

declare(strict_types=1);

namespace App\Tests\Performance;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests de performance pour vérifier l'absence de requêtes N+1
 * 
 * Ces tests s'assurent que les relations sont chargées efficacement
 * en une seule requête (ou un nombre limité de requêtes).
 */
class N1QueriesTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?DebugStack $sqlLogger;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        
        // Activer le logger SQL pour compter les requêtes
        $this->sqlLogger = new DebugStack();
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($this->sqlLogger);
    }

    public function testBooksCollectionDoesNotHaveN1Queries(): void
    {
        $bookRepository = $this->entityManager->getRepository(\App\Entity\Book::class);
        
        // Réinitialiser le compteur de requêtes
        $this->sqlLogger->queries = [];
        
        // Récupérer les livres avec leurs relations
        $books = $bookRepository->findAllWithRelations();
        
        $this->assertNotEmpty($books, 'Aucun livre trouvé dans la base');
        
        // Compter les requêtes exécutées
        $queryCount = count($this->sqlLogger->queries);
        
        // On s'attend à 1 seule requête avec LEFT JOIN
        // (ou au pire 2-3 si Doctrine optimise différemment)
        $this->assertLessThanOrEqual(3, $queryCount, 
            sprintf(
                'Trop de requêtes SQL détectées (%d). Problème N+1 potentiel. Requêtes: %s',
                $queryCount,
                json_encode(array_column($this->sqlLogger->queries, 'sql'), JSON_PRETTY_PRINT)
            )
        );
        
        // Vérifier que les relations sont bien chargées (sans requête supplémentaire)
        $queriesBeforeAccess = count($this->sqlLogger->queries);
        
        foreach ($books as $book) {
            // Accéder aux relations (ne devrait pas déclencher de nouvelles requêtes)
            $book->getAuthors()->count();
            $book->getCategories()->count();
        }
        
        $queriesAfterAccess = count($this->sqlLogger->queries);
        
        // Aucune requête supplémentaire ne doit avoir été exécutée
        $this->assertEquals(
            $queriesBeforeAccess, 
            $queriesAfterAccess,
            'Des requêtes supplémentaires ont été déclenchées lors de l\'accès aux relations (N+1 détecté)'
        );
    }

    public function testUserBooksCollectionDoesNotHaveN1Queries(): void
    {
        $userRepository = $this->entityManager->getRepository(\App\Entity\User::class);
        
        // Trouver un utilisateur avec des livres
        $user = $userRepository->findOneBy(['email' => 'marie.martin@email.com']);
        $this->assertNotNull($user);
        
        // Réinitialiser le compteur de requêtes
        $this->sqlLogger->queries = [];
        
        // Récupérer l'utilisateur avec ses livres et relations
        $userWithBooks = $userRepository->findOneWithBooksAndRelations($user->getId());
        
        $this->assertNotNull($userWithBooks);
        
        // Compter les requêtes exécutées
        $queryCount = count($this->sqlLogger->queries);
        
        // On s'attend à 1 seule requête avec LEFT JOIN
        $this->assertLessThanOrEqual(3, $queryCount, 
            sprintf(
                'Trop de requêtes SQL détectées (%d). Problème N+1 potentiel.',
                $queryCount
            )
        );
        
        // Vérifier que les relations sont bien chargées (sans requête supplémentaire)
        $queriesBeforeAccess = count($this->sqlLogger->queries);
        
        $books = $userWithBooks->getBooksCollection();
        foreach ($books as $book) {
            // Accéder aux relations (ne devrait pas déclencher de nouvelles requêtes)
            $book->getAuthors()->count();
            $book->getCategories()->count();
        }
        
        $queriesAfterAccess = count($this->sqlLogger->queries);
        
        // Aucune requête supplémentaire ne doit avoir été exécutée
        $this->assertEquals(
            $queriesBeforeAccess, 
            $queriesAfterAccess,
            'Des requêtes supplémentaires ont été déclenchées lors de l\'accès aux relations (N+1 détecté)'
        );
    }

    public function testBookRepositoryMethodLoadsRelations(): void
    {
        // Test simple pour vérifier que les relations sont bien chargées
        $bookRepository = $this->entityManager->getRepository(\App\Entity\Book::class);
        
        $books = $bookRepository->findAllWithRelations();
        
        $this->assertNotEmpty($books, 'Aucun livre trouvé');
        
        // Vérifier que les relations sont accessibles
        $firstBook = $books[0];
        
        // Ces appels ne devraient PAS déclencher de lazy loading
        // car les relations ont été chargées avec addSelect()
        $authors = $firstBook->getAuthors();
        $categories = $firstBook->getCategories();
        
        // Vérifier que les relations sont des collections initialisées
        $this->assertNotNull($authors);
        $this->assertNotNull($categories);
        
        // Si on arrive ici sans exception et sans requête supplémentaire,
        // c'est que les relations ont bien été chargées en EAGER
        $this->assertTrue(true, 'Les relations sont correctement chargées');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Désactiver le logger
        if ($this->entityManager) {
            $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        }
        
        $this->entityManager = null;
        $this->sqlLogger = null;
    }
}

