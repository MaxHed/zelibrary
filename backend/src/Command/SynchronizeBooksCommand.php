<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:synchronize-books',
    description: 'Synchronise les livres depuis l\'API Gutendex',
)]
class SynchronizeBooksCommand extends Command
{
    private string $apiBookUrl = "https://gutendex.com/books/?languages=fr";
    
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookRepository $bookRepository,
        private AuthorRepository $authorRepository,
        private CategoryRepository $categoryRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('pages', 'p', InputOption::VALUE_OPTIONAL, 'Nombre de pages à importer (défaut: toutes)', null)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forcer la mise à jour des livres existants')
            ->setHelp('Cette commande importe les livres depuis l\'API Gutendex.com');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pages = $input->getOption('pages');
        $force = $input->getOption('force');
        
        $io->title('Synchronisation des livres depuis Gutendex');
        
        $stats = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        $currentPage = 1;
        $maxPages = $pages ? (int)$pages : PHP_INT_MAX;
        
        try {
            do {
                $io->section("Traitement de la page $currentPage");
                
                $url = $currentPage === 1 
                    ? $this->apiBookUrl 
                    : $this->apiBookUrl . "&page=$currentPage";
                
                $response = HttpClient::create()->request('GET', $url);
                $data = $response->toArray();
                
                // dd($data);
                if (empty($data['results'])) {
                    $io->info('Aucun livre trouvé sur cette page');
                    break;
                }
                
                $pageStats = $this->processBooks($data['results'], $force, $io);
                
                // Mise à jour des statistiques
                foreach ($pageStats as $key => $value) {
                    $stats[$key] += $value;
                }
                
                $io->info(sprintf(
                    'Page %d: %d importés, %d mis à jour, %d ignorés, %d erreurs',
                    $currentPage,
                    $pageStats['imported'],
                    $pageStats['updated'],
                    $pageStats['skipped'],
                    $pageStats['errors']
                ));
                
                $currentPage++;
                
                // Vérifier s'il y a une page suivante
                if (!isset($data['next']) || $currentPage > $maxPages) {
                    break;
                }
                
                // Petite pause pour éviter de surcharger l'API
                sleep(1);
                
            } while ($currentPage <= $maxPages);
            
            $this->entityManager->flush();
            
            $io->success(sprintf(
                'Synchronisation terminée ! Total: %d importés, %d mis à jour, %d ignorés, %d erreurs',
                $stats['imported'],
                $stats['updated'],
                $stats['skipped'],
                $stats['errors']
            ));
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Erreur lors de la synchronisation : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function processBooks(array $books, bool $force, SymfonyStyle $io): array
    {
        $stats = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        foreach ($books as $bookData) {
            try {
                $titleCaracteresCount = strlen($bookData['title']);
                if ($titleCaracteresCount > 255) {
                    // Passer au livre suivant car le titre est trop long
                    // Nous n'en avons pas besoin pour la demonstration de l'application
                    continue;
                }
                $existingBook = $this->bookRepository->findOneBy(['gutendexId' => $bookData['id']]);
                
                if ($existingBook && !$force) {
                    $stats['skipped']++;
                    continue;
                }
                
                $book = $existingBook ?: new Book();
                
                $this->populateBookFromData($book, $bookData);
                
                if (!$existingBook) {
                    $this->entityManager->persist($book);
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }
                
            } catch (\Exception $e) {
                $io->error(sprintf('Erreur lors du traitement du livre ID %d: %s', $bookData['id'], $e->getMessage()));
                $stats['errors']++;
            }
        }
        
        return $stats;
    }
    
    private function populateBookFromData(Book $book, array $data): void
    {
        $book->setGutendexId($data['id'])
             ->setTitle($data['title'])
             ->setMediaType($data['media_type'])
             ->setCopyright($data['copyright']);
        
        // Traitement des auteurs
        if (!empty($data['authors'])) {
            foreach ($data['authors'] as $authorData) {
                $author = $this->authorRepository->findOrCreateByName(
                    $authorData['name'],
                    $authorData['birth_year'] ?? null,
                    $authorData['death_year'] ?? null
                );
                $book->addAuthor($author);
            }
        }
        
        // Traitement du résumé
        if (!empty($data['summaries'])) {
            $book->setSummary($data['summaries'][0]);
        }
        
        // Traitement des sujets
        if (!empty($data['subjects'])) {
            foreach ($data['subjects'] as $subjectName) {
                $category = $this->categoryRepository->findOrCreateByNameAndType($subjectName, 'subject');
                $book->addCategory($category);
            }
        }
        
        // Traitement des étagères
        if (!empty($data['bookshelves'])) {
            foreach ($data['bookshelves'] as $bookshelfName) {
                $category = $this->categoryRepository->findOrCreateByNameAndType($bookshelfName, 'bookshelf');
                $book->addCategory($category);
            }
        }
        
        // Traitement des langues
        if (!empty($data['languages'])) {
            $book->setLanguages($data['languages']);
        }
        
        // Traitement des formats
        if (!empty($data['formats'])) {
            $book->setFormats($data['formats']);
        }
        
        $book->setUpdatedAt(new \DateTimeImmutable());
    }
}
