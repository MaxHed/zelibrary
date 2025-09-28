<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private function slugify(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '_', $value);
        $value = trim($value, '_');
        return $value ?? '';
    }
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Fiction', 'type' => 'subject'],
            ['name' => 'Non-fiction', 'type' => 'subject'],
            ['name' => 'Science-fiction', 'type' => 'subject'],
            ['name' => 'Fantasy', 'type' => 'subject'],
            ['name' => 'Mystère', 'type' => 'subject'],
            ['name' => 'Thriller', 'type' => 'subject'],
            ['name' => 'Romance', 'type' => 'subject'],
            ['name' => 'Horreur', 'type' => 'subject'],
            ['name' => 'Policier', 'type' => 'subject'],
            ['name' => 'Biographie', 'type' => 'subject'],
            ['name' => 'Histoire', 'type' => 'subject'],
            ['name' => 'Philosophie', 'type' => 'subject'],
            ['name' => 'Poésie', 'type' => 'subject'],
            ['name' => 'Théâtre', 'type' => 'subject'],
            ['name' => 'Essai', 'type' => 'subject'],
            ['name' => 'Autobiographie', 'type' => 'subject'],
            ['name' => 'Mémoires', 'type' => 'subject'],
            ['name' => 'Journalisme', 'type' => 'subject'],
            ['name' => 'Voyage', 'type' => 'subject'],
            ['name' => 'Cuisine', 'type' => 'subject'],
            ['name' => 'Art', 'type' => 'subject'],
            ['name' => 'Musique', 'type' => 'subject'],
            ['name' => 'Cinéma', 'type' => 'subject'],
            ['name' => 'Photographie', 'type' => 'subject'],
            ['name' => 'Architecture', 'type' => 'subject'],
            ['name' => 'Design', 'type' => 'subject'],
            ['name' => 'Mode', 'type' => 'subject'],
            ['name' => 'Sport', 'type' => 'subject'],
            ['name' => 'Nature', 'type' => 'subject'],
            ['name' => 'Environnement', 'type' => 'subject'],
            ['name' => 'Sciences', 'type' => 'subject'],
            ['name' => 'Mathématiques', 'type' => 'subject'],
            ['name' => 'Physique', 'type' => 'subject'],
            ['name' => 'Chimie', 'type' => 'subject'],
            ['name' => 'Biologie', 'type' => 'subject'],
            ['name' => 'Médecine', 'type' => 'subject'],
            ['name' => 'Psychologie', 'type' => 'subject'],
            ['name' => 'Sociologie', 'type' => 'subject'],
            ['name' => 'Anthropologie', 'type' => 'subject'],
            ['name' => 'Économie', 'type' => 'subject'],
            ['name' => 'Politique', 'type' => 'subject'],
            ['name' => 'Droit', 'type' => 'subject'],
            ['name' => 'Éducation', 'type' => 'subject'],
            ['name' => 'Technologie', 'type' => 'subject'],
            ['name' => 'Informatique', 'type' => 'subject'],
            ['name' => 'Ingénierie', 'type' => 'subject'],
            ['name' => 'Business', 'type' => 'subject'],
            ['name' => 'Finance', 'type' => 'subject'],
            ['name' => 'Marketing', 'type' => 'subject'],
            ['name' => 'Management', 'type' => 'subject'],
            ['name' => 'Leadership', 'type' => 'subject'],
            ['name' => 'Développement personnel', 'type' => 'subject'],
            ['name' => 'Spiritualité', 'type' => 'subject'],
            ['name' => 'Religion', 'type' => 'subject'],
            ['name' => 'Mythologie', 'type' => 'subject'],
            ['name' => 'Légendes', 'type' => 'subject'],
            ['name' => 'Contes', 'type' => 'subject'],
            ['name' => 'Fables', 'type' => 'subject'],
            ['name' => 'Jeunesse', 'type' => 'bookshelf'],
            ['name' => 'Adolescent', 'type' => 'bookshelf'],
            ['name' => 'Adulte', 'type' => 'bookshelf'],
            ['name' => 'Enfant', 'type' => 'bookshelf'],
            ['name' => 'Bébé', 'type' => 'bookshelf'],
            ['name' => 'Étudiant', 'type' => 'bookshelf'],
            ['name' => 'Professionnel', 'type' => 'bookshelf'],
            ['name' => 'Senior', 'type' => 'bookshelf'],
            ['name' => 'Débutant', 'type' => 'bookshelf'],
            ['name' => 'Avancé', 'type' => 'bookshelf'],
            ['name' => 'Expert', 'type' => 'bookshelf'],
            ['name' => 'Classique', 'type' => 'bookshelf'],
            ['name' => 'Moderne', 'type' => 'bookshelf'],
            ['name' => 'Contemporain', 'type' => 'bookshelf'],
            ['name' => 'Vintage', 'type' => 'bookshelf'],
            ['name' => 'Rare', 'type' => 'bookshelf'],
            ['name' => 'Édition limitée', 'type' => 'bookshelf'],
            ['name' => 'Première édition', 'type' => 'bookshelf'],
            ['name' => 'Signé', 'type' => 'bookshelf'],
            ['name' => 'Dédicacé', 'type' => 'bookshelf']
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setType($categoryData['type']);
            
            $manager->persist($category);
            $asciiRef = 'category_' . $this->slugify($categoryData['name']);
            $this->addReference($asciiRef, $category);
            $legacyRef = 'category_' . strtolower(str_replace(' ', '_', $categoryData['name']));
            if ($legacyRef !== $asciiRef) {
                $this->addReference($legacyRef, $category);
            }
        }

        $manager->flush();
    }
}
