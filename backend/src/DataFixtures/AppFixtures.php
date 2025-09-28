<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Cette classe est le point d'entrée principal pour toutes les fixtures
        // Les fixtures individuelles sont chargées automatiquement par Doctrine
        // L'ordre de chargement est défini par les dépendances entre les entités
    }
}