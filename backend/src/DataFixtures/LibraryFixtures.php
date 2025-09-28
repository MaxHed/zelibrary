<?php

namespace App\DataFixtures;

use App\Entity\Library;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LibraryFixtures extends Fixture
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
        $libraries = [
            [
                'name' => 'Bibliothèque Nationale de France',
                'address' => 'Quai François Mauriac, 75706 Paris',
                'phone' => '01 53 79 59 59',
                'email' => 'contact@bnf.fr',
                'website' => 'https://www.bnf.fr',
                'description' => 'La Bibliothèque nationale de France est la bibliothèque nationale de la République française, héritière des collections royales constituées depuis le Moyen Âge.',
                'borrowLimit' => 20,
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Biblioth%C3%A8que_nationale_de_France_logo.svg/200px-Biblioth%C3%A8que_nationale_de_France_logo.svg.png'
            ],
            [
                'name' => 'Bibliothèque Sainte-Geneviève',
                'address' => '10 Place du Panthéon, 75005 Paris',
                'phone' => '01 44 41 97 97',
                'email' => 'bsg@univ-paris1.fr',
                'website' => 'https://www-bsg.univ-paris1.fr',
                'description' => 'Bibliothèque interuniversitaire de Paris, spécialisée dans les sciences humaines et sociales.',
                'borrowLimit' => 15,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque Mazarine',
                'address' => '23 Quai de Conti, 75006 Paris',
                'phone' => '01 44 41 44 06',
                'email' => 'contact@bibliotheque-mazarine.fr',
                'website' => 'https://www.bibliotheque-mazarine.fr',
                'description' => 'La plus ancienne bibliothèque publique de France, fondée en 1643.',
                'borrowLimit' => 10,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Arsenal',
                'address' => '1 Rue de Sully, 75004 Paris',
                'phone' => '01 53 79 39 39',
                'email' => 'arsenal@bnf.fr',
                'website' => 'https://www.bnf.fr/fr/la-bibliotheque-de-larsenal',
                'description' => 'Bibliothèque spécialisée dans la littérature française et les arts du spectacle.',
                'borrowLimit' => 12,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque Forney',
                'address' => '1 Rue du Figuier, 75004 Paris',
                'phone' => '01 42 78 14 60',
                'email' => 'bibliotheque.forney@paris.fr',
                'website' => 'https://www.paris.fr/pages/bibliotheque-forney-1810',
                'description' => 'Bibliothèque spécialisée dans les arts décoratifs, les métiers d\'art et les techniques.',
                'borrowLimit' => 8,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque Marguerite Durand',
                'address' => '79 Rue Nationale, 75013 Paris',
                'phone' => '01 45 70 80 00',
                'email' => 'bibliotheque.marguerite-durand@paris.fr',
                'website' => 'https://www.paris.fr/pages/bibliotheque-marguerite-durand-1811',
                'description' => 'Bibliothèque spécialisée dans l\'histoire des femmes et du féminisme.',
                'borrowLimit' => 10,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque Historique de la Ville de Paris',
                'address' => '24 Rue Pavée, 75004 Paris',
                'phone' => '01 44 59 29 60',
                'email' => 'bhvp@paris.fr',
                'website' => 'https://www.paris.fr/pages/bibliotheque-historique-de-la-ville-de-paris-1812',
                'description' => 'Conserve la mémoire de Paris à travers des documents historiques.',
                'borrowLimit' => 6,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Institut du Monde Arabe',
                'address' => '1 Rue des Fossés Saint-Bernard, 75005 Paris',
                'phone' => '01 40 51 38 38',
                'email' => 'bibliotheque@imarabe.org',
                'website' => 'https://www.imarabe.org/fr/bibliotheque',
                'description' => 'Spécialisée dans la culture et la civilisation arabes.',
                'borrowLimit' => 15,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de la Sorbonne',
                'address' => '17 Rue de la Sorbonne, 75005 Paris',
                'phone' => '01 40 46 22 11',
                'email' => 'bibliotheque.sorbonne@sorbonne-universite.fr',
                'website' => 'https://www.sorbonne-universite.fr/bibliotheques',
                'description' => 'Bibliothèque universitaire de la Sorbonne, riche en ouvrages anciens.',
                'borrowLimit' => 20,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'École Normale Supérieure',
                'address' => '45 Rue d\'Ulm, 75005 Paris',
                'phone' => '01 44 32 30 00',
                'email' => 'bibliotheque@ens.fr',
                'website' => 'https://www.ens.fr/bibliotheque',
                'description' => 'Bibliothèque de recherche en sciences humaines et sociales.',
                'borrowLimit' => 25,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Assemblée Nationale',
                'address' => '33 Quai d\'Orsay, 75007 Paris',
                'phone' => '01 40 63 60 00',
                'email' => 'bibliotheque@assemblee-nationale.fr',
                'website' => 'https://www.assemblee-nationale.fr/bibliotheque',
                'description' => 'Bibliothèque parlementaire spécialisée en droit et sciences politiques.',
                'borrowLimit' => 5,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque du Sénat',
                'address' => '15 Rue de Vaugirard, 75006 Paris',
                'phone' => '01 42 34 20 00',
                'email' => 'bibliotheque@senat.fr',
                'website' => 'https://www.senat.fr/bibliotheque',
                'description' => 'Bibliothèque parlementaire du Sénat français.',
                'borrowLimit' => 5,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Académie Française',
                'address' => '23 Quai de Conti, 75006 Paris',
                'phone' => '01 44 41 43 00',
                'email' => 'bibliotheque@academie-francaise.fr',
                'website' => 'https://www.academie-francaise.fr/bibliotheque',
                'description' => 'Bibliothèque de l\'Académie française, conservant la langue française.',
                'borrowLimit' => 3,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Institut de France',
                'address' => '23 Quai de Conti, 75006 Paris',
                'phone' => '01 44 41 43 00',
                'email' => 'bibliotheque@institut-de-france.fr',
                'website' => 'https://www.institut-de-france.fr/bibliotheque',
                'description' => 'Bibliothèque de l\'Institut de France, riche en manuscrits anciens.',
                'borrowLimit' => 3,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de la Cinémathèque Française',
                'address' => '51 Rue de Bercy, 75012 Paris',
                'phone' => '01 71 19 33 33',
                'email' => 'bibliotheque@cinematheque.fr',
                'website' => 'https://www.cinematheque.fr/bibliotheque',
                'description' => 'Spécialisée dans le cinéma et l\'audiovisuel.',
                'borrowLimit' => 10,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de la Cité de la Musique',
                'address' => '221 Avenue Jean Jaurès, 75019 Paris',
                'phone' => '01 44 84 44 84',
                'email' => 'bibliotheque@philharmoniedeparis.fr',
                'website' => 'https://philharmoniedeparis.fr/fr/bibliotheque',
                'description' => 'Spécialisée dans la musique et les arts du spectacle.',
                'borrowLimit' => 12,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'Opéra de Paris',
                'address' => '8 Rue Scribe, 75009 Paris',
                'phone' => '01 42 44 45 46',
                'email' => 'bibliotheque@operadeparis.fr',
                'website' => 'https://www.operadeparis.fr/bibliotheque',
                'description' => 'Spécialisée dans l\'opéra, la danse et les arts lyriques.',
                'borrowLimit' => 8,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de la Comédie-Française',
                'address' => '1 Place Colette, 75001 Paris',
                'phone' => '01 44 58 15 15',
                'email' => 'bibliotheque@comedie-francaise.fr',
                'website' => 'https://www.comedie-francaise.fr/bibliotheque',
                'description' => 'Spécialisée dans le théâtre français classique et contemporain.',
                'borrowLimit' => 6,
                'logo' => null
            ],
            [
                'name' => 'Bibliothèque de l\'École des Beaux-Arts',
                'address' => '14 Rue Bonaparte, 75006 Paris',
                'phone' => '01 47 03 50 00',
                'email' => 'bibliotheque@ensba.fr',
                'website' => 'https://www.ensba.fr/bibliotheque',
                'description' => 'Spécialisée dans les beaux-arts et l\'architecture.',
                'borrowLimit' => 15,
                'logo' => null
            ],
            // Grandes villes de France
            [ 'name' => 'Médiathèque José Cabanis', 'address' => '1 Allée Jacques Chaban-Delmas, 31500 Toulouse', 'phone' => '05 62 27 40 00', 'email' => 'contact@bm-toulouse.fr', 'website' => 'https://www.bibliotheque.toulouse.fr', 'description' => 'Grande médiathèque de Toulouse.', 'borrowLimit' => 15, 'logo' => null ],
            [ 'name' => 'BM Lyon Part-Dieu', 'address' => '30 Boulevard Marius Vivier Merle, 69003 Lyon', 'phone' => '04 78 62 18 00', 'email' => 'contact@bm-lyon.fr', 'website' => 'https://www.bm-lyon.fr', 'description' => 'Bibliothèque municipale de Lyon, site Part-Dieu.', 'borrowLimit' => 20, 'logo' => null ],
            [ 'name' => 'Bibliothèque Municipale de Marseille', 'address' => 'Alcazar, 58 Cours Belsunce, 13001 Marseille', 'phone' => '04 91 55 90 00', 'email' => 'contact@bm-marseille.fr', 'website' => 'https://www.bmvr.marseille.fr', 'description' => 'BMVR Alcazar à Marseille.', 'borrowLimit' => 15, 'logo' => null ],
            [ 'name' => 'Médiathèque de Lille', 'address' => "36 Rue Edouard Delesalle, 59800 Lille", 'phone' => '03 20 15 97 15', 'email' => 'contact@bm-lille.fr', 'website' => 'https://bm-lille.fr', 'description' => 'Réseau des médiathèques de Lille.', 'borrowLimit' => 12, 'logo' => null ],
            [ 'name' => 'Médiathèque de Nantes', 'address' => '15 Rue de l’Hôtel de Ville, 44000 Nantes', 'phone' => '02 40 41 95 95', 'email' => 'contact@bm-nantes.fr', 'website' => 'https://bm.nantes.fr', 'description' => 'Médiathèques de Nantes.', 'borrowLimit' => 12, 'logo' => null ],
            [ 'name' => 'Médiathèque de Bordeaux', 'address' => '85 Cours du Maréchal Juin, 33000 Bordeaux', 'phone' => '05 56 10 30 00', 'email' => 'contact@bm-bordeaux.fr', 'website' => 'https://bibliotheque.bordeaux.fr', 'description' => 'Bibliothèques de Bordeaux.', 'borrowLimit' => 12, 'logo' => null ],
            [ 'name' => 'Médiathèque de Nice', 'address' => '8 Avenue Félix Faure, 06000 Nice', 'phone' => '04 97 13 49 00', 'email' => 'contact@bm-nice.fr', 'website' => 'https://bmvr.nice.fr', 'description' => 'BMVR Louis Nucéra à Nice.', 'borrowLimit' => 12, 'logo' => null ],
            [ 'name' => 'Médiathèque de Strasbourg', 'address' => '1 Place de l’Europe, 67000 Strasbourg', 'phone' => '03 88 45 10 10', 'email' => 'contact@bm-strasbourg.fr', 'website' => 'https://www.mediatheques.strasbourg.eu', 'description' => 'Médiathèques de l’Eurométropole de Strasbourg.', 'borrowLimit' => 15, 'logo' => null ],
            [ 'name' => 'Médiathèque Rennes Métropole', 'address' => '10 Cours des Alliés, 35000 Rennes', 'phone' => '02 23 62 26 40', 'email' => 'contact@bm-rennes.fr', 'website' => 'https://www.bibliotheques.rennes.fr', 'description' => 'Les Champs Libres.', 'borrowLimit' => 15, 'logo' => null ],
            [ 'name' => 'Médiathèque de Montpellier', 'address' => 'Rue de l’Herault, 34000 Montpellier', 'phone' => '04 67 34 70 00', 'email' => 'contact@bm-montpellier.fr', 'website' => 'https://mediatheques.montpellier3m.fr', 'description' => 'Réseau des médiathèques de Montpellier.', 'borrowLimit' => 12, 'logo' => null ],
            [ 'name' => 'Médiathèque de Toulouse - Minimes', 'address' => '3 Avenue des Minimes, 31200 Toulouse', 'phone' => '05 62 27 62 27', 'email' => 'minimes@bm-toulouse.fr', 'website' => 'https://www.bibliotheque.toulouse.fr', 'description' => 'Annexe de la médiathèque José Cabanis.', 'borrowLimit' => 8, 'logo' => null ],
        ];

        foreach ($libraries as $libraryData) {
            $library = new Library();
            $library->setName($libraryData['name']);
            $library->setAddress($libraryData['address']);
            $library->setPhone($libraryData['phone']);
            $library->setEmail($libraryData['email']);
            $library->setWebsite($libraryData['website']);
            $library->setDescription($libraryData['description']);
            $library->setBorrowLimit($libraryData['borrowLimit']);
            $library->setLogo($libraryData['logo']);
            $library->setCreatedAt(new \DateTimeImmutable());
            $library->setUpdatedAt(new \DateTimeImmutable());
            
            $manager->persist($library);
            $asciiRef = 'library_' . $this->slugify($libraryData['name']);
            $this->addReference($asciiRef, $library);
            $legacyRef = 'library_' . strtolower(str_replace([' ', '\'', '-'], ['_', '', '_'], $libraryData['name']));
            if ($legacyRef !== $asciiRef) {
                $this->addReference($legacyRef, $library);
            }
        }

        $manager->flush();
    }
}
