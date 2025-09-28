<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            BookFixtures::class,
            UserFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $reviews = [
            [
                'book' => 'les_miserables',
                'user' => 'jean.dupont@email.com',
                'review' => 'Un chef-d\'œuvre absolu ! L\'histoire de Jean Valjean est bouleversante et la description de la société française du XIXe siècle est remarquable.',
                'rate' => 5
            ],
            [
                'book' => 'les_miserables',
                'user' => 'marie.martin@email.com',
                'review' => 'Très long mais passionnant. Hugo a un style unique et les personnages sont inoubliables.',
                'rate' => 4
            ],
            [
                'book' => 'madame_bovary',
                'user' => 'pierre.durand@email.com',
                'review' => 'Flaubert maîtrise parfaitement l\'art du roman. Emma Bovary est un personnage complexe et attachant.',
                'rate' => 5
            ],
            [
                'book' => 'germinal',
                'user' => 'sophie.bernard@email.com',
                'review' => 'Un roman puissant sur la condition ouvrière. Zola décrit avec réalisme la vie des mineurs.',
                'rate' => 4
            ],
            [
                'book' => 'a_la_recherche_du_temps_perdu',
                'user' => 'emma.roux@email.com',
                'review' => 'Une œuvre monumentale ! Proust explore la mémoire et le temps avec une profondeur inégalée.',
                'rate' => 5
            ],
            [
                'book' => 'l_etranger',
                'user' => 'thomas.moreau@email.com',
                'review' => 'Camus aborde des questions existentielles fondamentales. Un roman court mais intense.',
                'rate' => 4
            ],
            [
                'book' => 'hamlet',
                'user' => 'lea.simon@email.com',
                'review' => 'Shakespeare au sommet de son art. Les monologues d\'Hamlet sont d\'une beauté incomparable.',
                'rate' => 5
            ],
            [
                'book' => 'great_expectations',
                'user' => 'hugo.laurent@email.com',
                'review' => 'Dickens excelle dans la description de la société victorienne. Pip est un personnage attachant.',
                'rate' => 4
            ],
            [
                'book' => 'pride_and_prejudice',
                'user' => 'chloe.david@email.com',
                'review' => 'Un classique de la littérature anglaise ! L\'ironie d\'Austen est délicieuse.',
                'rate' => 5
            ],
            [
                'book' => 'the_adventures_of_tom_sawyer',
                'user' => 'alexandre.robert@email.com',
                'review' => 'Un roman d\'aventures captivant. Twain capture parfaitement l\'esprit de l\'enfance.',
                'rate' => 4
            ],
            [
                'book' => 'the_picture_of_dorian_gray',
                'user' => 'julie.richard@email.com',
                'review' => 'Wilde explore brillamment les thèmes de la beauté et de la corruption. Un roman fascinant.',
                'rate' => 5
            ],
            [
                'book' => '1984',
                'user' => 'antoine.thomas@email.com',
                'review' => 'Une dystopie terrifiante et prémonitoire. Orwell nous met en garde contre le totalitarisme.',
                'rate' => 5
            ],
            [
                'book' => 'the_lord_of_the_rings',
                'user' => 'camille.petit@email.com',
                'review' => 'L\'épopée fantasy par excellence ! Tolkien a créé un monde d\'une richesse incroyable.',
                'rate' => 5
            ],
            [
                'book' => 'murder_on_the_orient_express',
                'user' => 'maxime.michaud@email.com',
                'review' => 'Un mystère parfaitement construit. Christie est la reine du roman policier.',
                'rate' => 4
            ],
            [
                'book' => 'foundation',
                'user' => 'laura.garcia@email.com',
                'review' => 'Asimov révolutionne la science-fiction. La psychohistoire est un concept génial.',
                'rate' => 5
            ],
            [
                'book' => 'fahrenheit_451',
                'user' => 'quentin.dubois@email.com',
                'review' => 'Bradbury nous alerte sur les dangers de la censure. Un roman visionnaire.',
                'rate' => 4
            ],
            [
                'book' => 'les_miserables',
                'user' => 'manon.moreau@email.com',
                'review' => 'Un peu long à mon goût, mais l\'histoire est magnifique. Cosette et Gavroche sont des personnages inoubliables.',
                'rate' => 3
            ],
            [
                'book' => 'madame_bovary',
                'user' => 'nathan.simon@email.com',
                'review' => 'Flaubert a un style très descriptif. Intéressant mais parfois un peu lourd.',
                'rate' => 3
            ],
            [
                'book' => 'germinal',
                'user' => 'oceane.laurent@email.com',
                'review' => 'Un roman social puissant. Zola dénonce les injustices avec brio.',
                'rate' => 4
            ],
            [
                'book' => 'a_la_recherche_du_temps_perdu',
                'user' => 'jean.dupont@email.com',
                'review' => 'Difficile à lire mais enrichissant. Proust a une vision unique de la littérature.',
                'rate' => 4
            ],
            [
                'book' => 'l_etranger',
                'user' => 'marie.martin@email.com',
                'review' => 'Un roman philosophique profond. Camus questionne l\'absurdité de l\'existence.',
                'rate' => 5
            ],
            [
                'book' => 'hamlet',
                'user' => 'pierre.durand@email.com',
                'review' => 'Shakespeare est un génie. Hamlet reste d\'une actualité troublante.',
                'rate' => 5
            ],
            [
                'book' => 'great_expectations',
                'user' => 'sophie.bernard@email.com',
                'review' => 'Dickens excelle dans la peinture sociale. Un roman touchant et bien construit.',
                'rate' => 4
            ],
            [
                'book' => 'pride_and_prejudice',
                'user' => 'lucas.petit@email.com',
                'review' => 'Austen a un humour subtil et délicat. Elizabeth Bennet est un personnage formidable.',
                'rate' => 5
            ],
            [
                'book' => 'the_adventures_of_tom_sawyer',
                'user' => 'emma.roux@email.com',
                'review' => 'Un roman d\'aventures plein de charme. Twain capture l\'innocence de l\'enfance.',
                'rate' => 4
            ],
            [
                'book' => 'the_picture_of_dorian_gray',
                'user' => 'thomas.moreau@email.com',
                'review' => 'Wilde explore les thèmes de la beauté et de la moralité avec élégance.',
                'rate' => 4
            ],
            [
                'book' => '1984',
                'user' => 'lea.simon@email.com',
                'review' => 'Une dystopie glaçante. Orwell nous met en garde contre les dérives autoritaires.',
                'rate' => 5
            ],
            [
                'book' => 'the_lord_of_the_rings',
                'user' => 'hugo.laurent@email.com',
                'review' => 'Tolkien a créé un univers d\'une richesse inégalée. Une épopée fantastique magistrale.',
                'rate' => 5
            ],
            [
                'book' => 'murder_on_the_orient_express',
                'user' => 'chloe.david@email.com',
                'review' => 'Un mystère bien ficelé. Christie maîtrise parfaitement l\'art du suspense.',
                'rate' => 4
            ],
            [
                'book' => 'foundation',
                'user' => 'alexandre.robert@email.com',
                'review' => 'Asimov révolutionne la science-fiction. La psychohistoire est un concept brillant.',
                'rate' => 5
            ],
            [
                'book' => 'fahrenheit_451',
                'user' => 'julie.richard@email.com',
                'review' => 'Bradbury nous alerte sur les dangers de la censure. Un roman visionnaire et troublant.',
                'rate' => 4
            ]
        ];

        // Générer des reviews auto pour les livres démo
        for ($i = 1; $i <= 35; $i++) {
            $reviews[] = [
                'book' => 'livre_demo_' . $i, // sera mappé via addReference si ajouté, sinon ignoré
                'user' => 'jean.dupont@email.com',
                'review' => "Très bon livre démo $i",
                'rate' => rand(3,5)
            ];
        }

        foreach ($reviews as $reviewData) {
            $review = new Review();
            $review->setReview($reviewData['review']);
            $review->setRate($reviewData['rate']);
            $review->setCreatedAt(new \DateTimeImmutable());

            // Associer le livre
            $bookRef = 'book_' . $reviewData['book'];
            if (!$this->hasReference($bookRef, \App\Entity\Book::class)) {
                continue;
            }
            $book = $this->getReference($bookRef, \App\Entity\Book::class);
            $review->setBook($book);

            // Associer l'utilisateur
            $userRef = 'user_' . str_replace('@', '_at_', $reviewData['user']);
            if (!$this->hasReference($userRef, \App\Entity\User::class)) {
                continue;
            }
            $user = $this->getReference($userRef, \App\Entity\User::class);
            $review->setUser($user);

            $manager->persist($review);
        }

        $manager->flush();
    }
}
