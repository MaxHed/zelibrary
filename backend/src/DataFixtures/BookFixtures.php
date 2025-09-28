<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Library;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    private function slugify(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '_', $value);
        $value = trim($value, '_');
        return $value ?? '';
    }
    public function getDependencies(): array
    {
        return [
            AuthorFixtures::class,
            CategoryFixtures::class,
            LibraryFixtures::class,
            UserFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $books = [
            [
                'title' => 'Les Misérables',
                'summary' => 'Roman de Victor Hugo publié en 1862. L\'histoire se déroule en France au début du XIXe siècle et suit la vie de Jean Valjean, un ancien forçat.',
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 135,
                'authors' => ['victor_hugo'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve'],
                'users' => ['jean.dupont@email.com', 'marie.martin@email.com']
            ],
            [
                'title' => 'Madame Bovary',
                'summary' => 'Roman de Gustave Flaubert publié en 1857. L\'histoire d\'Emma Bovary, une femme mariée qui cherche à échapper à la monotonie de sa vie provinciale.',
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5141,
                'authors' => ['gustave_flaubert'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_mazarine'],
                'users' => ['pierre.durand@email.com']
            ],
            [
                'title' => 'Germinal',
                'summary' => 'Roman d\'Émile Zola publié en 1885. L\'histoire des mineurs de charbon dans le nord de la France au XIXe siècle.',
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5142,
                'authors' => ['emile_zola'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_sainte_genevieve'],
                'users' => ['sophie.bernard@email.com', 'lucas.petit@email.com']
            ],
            [
                'title' => 'À la recherche du temps perdu',
                'summary' => 'Cycle romanesque de Marcel Proust publié entre 1913 et 1927. Une exploration de la mémoire et du temps à travers les souvenirs du narrateur.',
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5143,
                'authors' => ['marcel_proust'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['emma.roux@email.com']
            ],
            [
                'title' => 'L\'Étranger',
                'summary' => 'Roman d\'Albert Camus publié en 1942. L\'histoire de Meursault, un homme indifférent qui commet un meurtre apparemment sans raison.',
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5144,
                'authors' => ['albert_camus'],
                'categories' => ['fiction', 'philosophie'],
                'libraries' => ['bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['thomas.moreau@email.com', 'lea.simon@email.com']
            ],
            [
                'title' => 'Hamlet',
                'summary' => 'Tragédie de William Shakespeare écrite vers 1600. L\'histoire du prince Hamlet qui cherche à venger la mort de son père.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5145,
                'authors' => ['william_shakespeare'],
                'categories' => ['théâtre', 'classique'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve'],
                'users' => ['hugo.laurent@email.com']
            ],
            [
                'title' => 'Great Expectations',
                'summary' => 'Roman de Charles Dickens publié en 1861. L\'histoire de Pip, un jeune orphelin qui rêve de devenir un gentleman.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5146,
                'authors' => ['charles_dickens'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_nationale_de_france'],
                'users' => ['chloe.david@email.com', 'alexandre.robert@email.com']
            ],
            [
                'title' => 'Pride and Prejudice',
                'summary' => 'Roman de Jane Austen publié en 1813. L\'histoire d\'Elizabeth Bennet et de sa relation avec Mr. Darcy.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5147,
                'authors' => ['jane_austen'],
                'categories' => ['fiction', 'romance', 'classique'],
                'libraries' => ['bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['julie.richard@email.com']
            ],
            [
                'title' => 'The Adventures of Tom Sawyer',
                'summary' => 'Roman de Mark Twain publié en 1876. Les aventures d\'un jeune garçon dans le Missouri au XIXe siècle.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5148,
                'authors' => ['mark_twain'],
                'categories' => ['fiction', 'jeunesse'],
                'libraries' => ['bibliotheque_nationale_de_france'],
                'users' => ['antoine.thomas@email.com', 'camille.petit@email.com']
            ],
            [
                'title' => 'The Picture of Dorian Gray',
                'summary' => 'Roman d\'Oscar Wilde publié en 1890. L\'histoire d\'un jeune homme qui reste éternellement jeune tandis que son portrait vieillit.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5149,
                'authors' => ['oscar_wilde'],
                'categories' => ['fiction', 'classique'],
                'libraries' => ['bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['maxime.michaud@email.com']
            ],
            [
                'title' => '1984',
                'summary' => 'Roman dystopique de George Orwell publié en 1949. L\'histoire d\'un monde totalitaire où la liberté de pensée est supprimée.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5150,
                'authors' => ['george_orwell'],
                'categories' => ['science_fiction', 'fiction'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve'],
                'users' => ['laura.garcia@email.com', 'quentin.dubois@email.com']
            ],
            [
                'title' => 'The Lord of the Rings',
                'summary' => 'Trilogie de fantasy de J.R.R. Tolkien publiée entre 1954 et 1955. L\'épopée de Frodo Baggins et de l\'Anneau Unique.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5151,
                'authors' => ['j.r.r._tolkien'],
                'categories' => ['fantasy', 'fiction'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['manon.moreau@email.com', 'nathan.simon@email.com']
            ],
            [
                'title' => 'Murder on the Orient Express',
                'summary' => 'Roman policier d\'Agatha Christie publié en 1934. L\'enquête d\'Hercule Poirot sur un meurtre commis dans le train.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5152,
                'authors' => ['agatha_christie'],
                'categories' => ['mystère', 'policier'],
                'libraries' => ['bibliotheque_sainte_genevieve'],
                'users' => ['oceane.laurent@email.com']
            ],
            [
                'title' => 'Foundation',
                'summary' => 'Série de science-fiction d\'Isaac Asimov publiée à partir de 1951. L\'histoire de la Fondation, une organisation qui préserve la connaissance humaine.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5153,
                'authors' => ['isaac_asimov'],
                'categories' => ['science_fiction'],
                'libraries' => ['bibliotheque_nationale_de_france', 'bibliotheque_sainte_genevieve'],
                'users' => ['jean.dupont@email.com', 'marie.martin@email.com']
            ],
            [
                'title' => 'Fahrenheit 451',
                'summary' => 'Roman dystopique de Ray Bradbury publié en 1953. L\'histoire d\'un monde où les livres sont interdits et brûlés.',
                'languages' => ['en'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip', 'text/plain'],
                'gutendexId' => 5154,
                'authors' => ['ray_bradbury'],
                'categories' => ['science_fiction', 'fiction'],
                'libraries' => ['bibliotheque_sainte_genevieve', 'bibliotheque_mazarine'],
                'users' => ['pierre.durand@email.com']
            ]
        ];

        // Générer 35 livres supplémentaires basés sur quelques auteurs/catégories aléatoires
        for ($i = 1; $i <= 35; $i++) {
            $books[] = [
                'title' => "Livre Démo $i",
                'summary' => "Résumé fictif du livre démo $i pour tests.",
                'languages' => ['fr'],
                'copyright' => false,
                'mediaType' => 'Text',
                'formats' => ['text/html', 'application/epub+zip'],
                'gutendexId' => 6000 + $i,
                'authors' => ['victor_hugo', 'emile_zola', 'george_orwell'][($i)%3] ? [ ['victor_hugo','emile_zola','george_orwell'][($i)%3] ] : ['victor_hugo'],
                'categories' => ['fiction','science_fiction','fantasy'][($i)%3] ? [ ['fiction','science_fiction','fantasy'][($i)%3] ] : ['fiction'],
                'libraries' => ['library_bibliotheque_nationale_de_france','library_bm_lyon_part_dieu','library_mediatheque_jose_cabanis'],
                'users' => []
            ];
        }

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setSummary($bookData['summary']);
            $book->setLanguages($bookData['languages']);
            $book->setCopyright($bookData['copyright']);
            $book->setMediaType($bookData['mediaType']);
            $book->setFormats($bookData['formats']);
            $book->setGutendexId($bookData['gutendexId']);
            $book->setCreatedAt(new \DateTimeImmutable());
            $book->setUpdatedAt(new \DateTimeImmutable());

            // Ajouter les auteurs
            foreach ($bookData['authors'] as $authorRef) {
                if ($this->hasReference('author_' . $authorRef, Author::class)) {
                    $author = $this->getReference('author_' . $authorRef, Author::class);
                    $book->addAuthor($author);
                }
            }

            // Ajouter les catégories
            foreach ($bookData['categories'] as $categoryRef) {
                if ($this->hasReference('category_' . $categoryRef, Category::class)) {
                    $category = $this->getReference('category_' . $categoryRef, Category::class);
                    $book->addCategory($category);
                }
            }

            // Ajouter aux bibliothèques
            foreach ($bookData['libraries'] as $libraryRef) {
                if ($this->hasReference($libraryRef, Library::class)) {
                    $library = $this->getReference($libraryRef, Library::class);
                    $book->addLibrary($library);
                }
            }

            // Ajouter aux collections d'utilisateurs
            foreach ($bookData['users'] as $userEmail) {
                $userRef = 'user_' . str_replace('@', '_at_', $userEmail);
                if ($this->hasReference($userRef, User::class)) {
                    $user = $this->getReference($userRef, User::class);
                    $user->addBooksCollection($book);
                }
            }

            $manager->persist($book);
            // Références cohérentes (ASCII + legacy)
            $asciiRef = 'book_' . $this->slugify($bookData['title']);
            $this->addReference($asciiRef, $book);
            $legacyRef = 'book_' . strtolower(str_replace([' ', '\'', '-'], ['_', '', '_'], $bookData['title']));
            if ($legacyRef !== $asciiRef) {
                $this->addReference($legacyRef, $book);
            }
        }

        $manager->flush();
    }
}
