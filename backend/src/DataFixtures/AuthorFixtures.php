<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
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
        $authors = [
            [
                'name' => 'Victor Hugo',
                'birthYear' => 1802,
                'deathYear' => 1885
            ],
            [
                'name' => 'Gustave Flaubert',
                'birthYear' => 1821,
                'deathYear' => 1880
            ],
            [
                'name' => 'Émile Zola',
                'birthYear' => 1840,
                'deathYear' => 1902
            ],
            [
                'name' => 'Marcel Proust',
                'birthYear' => 1871,
                'deathYear' => 1922
            ],
            [
                'name' => 'Albert Camus',
                'birthYear' => 1913,
                'deathYear' => 1960
            ],
            [
                'name' => 'Jean-Paul Sartre',
                'birthYear' => 1905,
                'deathYear' => 1980
            ],
            [
                'name' => 'Simone de Beauvoir',
                'birthYear' => 1908,
                'deathYear' => 1986
            ],
            [
                'name' => 'Antoine de Saint-Exupéry',
                'birthYear' => 1900,
                'deathYear' => 1944
            ],
            [
                'name' => 'Marguerite Duras',
                'birthYear' => 1914,
                'deathYear' => 1996
            ],
            [
                'name' => 'Boris Vian',
                'birthYear' => 1920,
                'deathYear' => 1959
            ],
            [
                'name' => 'William Shakespeare',
                'birthYear' => 1564,
                'deathYear' => 1616
            ],
            [
                'name' => 'Charles Dickens',
                'birthYear' => 1812,
                'deathYear' => 1870
            ],
            [
                'name' => 'Jane Austen',
                'birthYear' => 1775,
                'deathYear' => 1817
            ],
            [
                'name' => 'Mark Twain',
                'birthYear' => 1835,
                'deathYear' => 1910
            ],
            [
                'name' => 'Oscar Wilde',
                'birthYear' => 1854,
                'deathYear' => 1900
            ],
            [
                'name' => 'Virginia Woolf',
                'birthYear' => 1882,
                'deathYear' => 1941
            ],
            [
                'name' => 'George Orwell',
                'birthYear' => 1903,
                'deathYear' => 1950
            ],
            [
                'name' => 'J.R.R. Tolkien',
                'birthYear' => 1892,
                'deathYear' => 1973
            ],
            [
                'name' => 'Agatha Christie',
                'birthYear' => 1890,
                'deathYear' => 1976
            ],
            [
                'name' => 'Isaac Asimov',
                'birthYear' => 1920,
                'deathYear' => 1992
            ],
            [
                'name' => 'Ray Bradbury',
                'birthYear' => 1920,
                'deathYear' => 2012
            ],
            [
                'name' => 'Philip K. Dick',
                'birthYear' => 1928,
                'deathYear' => 1982
            ],
            [
                'name' => 'Ursula K. Le Guin',
                'birthYear' => 1929,
                'deathYear' => 2018
            ],
            [
                'name' => 'Frank Herbert',
                'birthYear' => 1920,
                'deathYear' => 1986
            ],
            [
                'name' => 'Arthur C. Clarke',
                'birthYear' => 1917,
                'deathYear' => 2008
            ],
            [
                'name' => 'Robert Heinlein',
                'birthYear' => 1907,
                'deathYear' => 1988
            ],
            [
                'name' => 'H.G. Wells',
                'birthYear' => 1866,
                'deathYear' => 1946
            ],
            [
                'name' => 'Jules Verne',
                'birthYear' => 1828,
                'deathYear' => 1905
            ],
            [
                'name' => 'Mary Shelley',
                'birthYear' => 1797,
                'deathYear' => 1851
            ],
            [
                'name' => 'Bram Stoker',
                'birthYear' => 1847,
                'deathYear' => 1912
            ],
            [
                'name' => 'Edgar Allan Poe',
                'birthYear' => 1809,
                'deathYear' => 1849
            ],
            [
                'name' => 'H.P. Lovecraft',
                'birthYear' => 1890,
                'deathYear' => 1937
            ],
            [
                'name' => 'Stephen King',
                'birthYear' => 1947,
                'deathYear' => null
            ],
            [
                'name' => 'Neil Gaiman',
                'birthYear' => 1960,
                'deathYear' => null
            ],
            [
                'name' => 'Terry Pratchett',
                'birthYear' => 1948,
                'deathYear' => 2015
            ],
            [
                'name' => 'Douglas Adams',
                'birthYear' => 1952,
                'deathYear' => 2001
            ],
            [
                'name' => 'Isaac Bashevis Singer',
                'birthYear' => 1902,
                'deathYear' => 1991
            ],
            [
                'name' => 'Gabriel García Márquez',
                'birthYear' => 1927,
                'deathYear' => 2014
            ],
            [
                'name' => 'Milan Kundera',
                'birthYear' => 1929,
                'deathYear' => 2023
            ],
            [
                'name' => 'Umberto Eco',
                'birthYear' => 1932,
                'deathYear' => 2016
            ],
            [
                'name' => 'Italo Calvino',
                'birthYear' => 1923,
                'deathYear' => 1985
            ],
            [
                'name' => 'Jorge Luis Borges',
                'birthYear' => 1899,
                'deathYear' => 1986
            ],
            [
                'name' => 'Pablo Neruda',
                'birthYear' => 1904,
                'deathYear' => 1973
            ],
            [
                'name' => 'Octavio Paz',
                'birthYear' => 1914,
                'deathYear' => 1998
            ],
            [
                'name' => 'Mario Vargas Llosa',
                'birthYear' => 1936,
                'deathYear' => null
            ],
            [
                'name' => 'Isabel Allende',
                'birthYear' => 1942,
                'deathYear' => null
            ],
            [
                'name' => 'Paulo Coelho',
                'birthYear' => 1947,
                'deathYear' => null
            ],
            [
                'name' => 'Haruki Murakami',
                'birthYear' => 1949,
                'deathYear' => null
            ],
            [
                'name' => 'Kazuo Ishiguro',
                'birthYear' => 1954,
                'deathYear' => null
            ],
            [
                'name' => 'Salman Rushdie',
                'birthYear' => 1947,
                'deathYear' => null
            ],
            [
                'name' => 'Arundhati Roy',
                'birthYear' => 1961,
                'deathYear' => null
            ],
            [
                'name' => 'Chimamanda Ngozi Adichie',
                'birthYear' => 1977,
                'deathYear' => null
            ],
            [
                'name' => 'Zadie Smith',
                'birthYear' => 1975,
                'deathYear' => null
            ],
            [
                'name' => 'Margaret Atwood',
                'birthYear' => 1939,
                'deathYear' => null
            ],
            [
                'name' => 'Donna Tartt',
                'birthYear' => 1963,
                'deathYear' => null
            ],
            [
                'name' => 'Jonathan Franzen',
                'birthYear' => 1959,
                'deathYear' => null
            ],
            [
                'name' => 'David Foster Wallace',
                'birthYear' => 1962,
                'deathYear' => 2008
            ],
            [
                'name' => 'Cormac McCarthy',
                'birthYear' => 1933,
                'deathYear' => 2023
            ],
            [
                'name' => 'Toni Morrison',
                'birthYear' => 1931,
                'deathYear' => 2019
            ],
            [
                'name' => 'Alice Walker',
                'birthYear' => 1944,
                'deathYear' => null
            ],
            [
                'name' => 'Maya Angelou',
                'birthYear' => 1928,
                'deathYear' => 2014
            ],
            [
                'name' => 'James Baldwin',
                'birthYear' => 1924,
                'deathYear' => 1987
            ],
            [
                'name' => 'Ralph Ellison',
                'birthYear' => 1914,
                'deathYear' => 1994
            ],
            [
                'name' => 'Zora Neale Hurston',
                'birthYear' => 1891,
                'deathYear' => 1960
            ],
            [
                'name' => 'Langston Hughes',
                'birthYear' => 1902,
                'deathYear' => 1967
            ],
            [
                'name' => 'Richard Wright',
                'birthYear' => 1908,
                'deathYear' => 1960
            ],
            [
                'name' => 'Amiri Baraka',
                'birthYear' => 1934,
                'deathYear' => 2014
            ],
            [
                'name' => 'Octavia Butler',
                'birthYear' => 1947,
                'deathYear' => 2006
            ],
            [
                'name' => 'Samuel R. Delany',
                'birthYear' => 1942,
                'deathYear' => null
            ],
            [
                'name' => 'N.K. Jemisin',
                'birthYear' => 1972,
                'deathYear' => null
            ],
            [
                'name' => 'Nnedi Okorafor',
                'birthYear' => 1974,
                'deathYear' => null
            ],
            [
                'name' => 'Rivers Solomon',
                'birthYear' => 1985,
                'deathYear' => null
            ],
            [
                'name' => 'Akwaeke Emezi',
                'birthYear' => 1987,
                'deathYear' => null
            ],
            [
                'name' => 'Tommy Orange',
                'birthYear' => 1982,
                'deathYear' => null
            ],
            [
                'name' => 'Louise Erdrich',
                'birthYear' => 1954,
                'deathYear' => null
            ],
            [
                'name' => 'Sherman Alexie',
                'birthYear' => 1966,
                'deathYear' => null
            ],
            [
                'name' => 'Joy Harjo',
                'birthYear' => 1951,
                'deathYear' => null
            ],
            [
                'name' => 'Leslie Marmon Silko',
                'birthYear' => 1948,
                'deathYear' => null
            ],
            [
                'name' => 'N. Scott Momaday',
                'birthYear' => 1934,
                'deathYear' => null
            ],
            [
                'name' => 'Gerald Vizenor',
                'birthYear' => 1934,
                'deathYear' => null
            ],
            [
                'name' => 'Diane Glancy',
                'birthYear' => 1941,
                'deathYear' => null
            ],
            [
                'name' => 'LeAnne Howe',
                'birthYear' => 1951,
                'deathYear' => null
            ],
            [
                'name' => 'Heid E. Erdrich',
                'birthYear' => 1963,
                'deathYear' => null
            ],
            [
                'name' => 'Billy-Ray Belcourt',
                'birthYear' => 1990,
                'deathYear' => null
            ],
            [
                'name' => 'Joshua Whitehead',
                'birthYear' => 1988,
                'deathYear' => null
            ],
            [
                'name' => 'Eden Robinson',
                'birthYear' => 1968,
                'deathYear' => null
            ],
            [
                'name' => 'Richard Wagamese',
                'birthYear' => 1955,
                'deathYear' => 2017
            ],
            [
                'name' => 'Thomas King',
                'birthYear' => 1943,
                'deathYear' => null
            ],
            [
                'name' => 'Drew Hayden Taylor',
                'birthYear' => 1962,
                'deathYear' => null
            ],
            [
                'name' => 'Cherie Dimaline',
                'birthYear' => 1975,
                'deathYear' => null
            ],
            [
                'name' => 'Katherena Vermette',
                'birthYear' => 1977,
                'deathYear' => null
            ],
            [
                'name' => 'Tanya Talaga',
                'birthYear' => 1975,
                'deathYear' => null
            ],
            [
                'name' => 'Waubgeshig Rice',
                'birthYear' => 1979,
                'deathYear' => null
            ],
            [
                'name' => 'Alicia Elliott',
                'birthYear' => 1985,
                'deathYear' => null
            ],
            [
                'name' => 'Jesse Thistle',
                'birthYear' => 1978,
                'deathYear' => null
            ],
            [
                'name' => 'Tanya Tagaq',
                'birthYear' => 1975,
                'deathYear' => null
            ],
            [
                'name' => 'Nunavut',
                'birthYear' => null,
                'deathYear' => null
            ],
            [
                'name' => 'Auteur Inconnu',
                'birthYear' => null,
                'deathYear' => null
            ],
            [
                'name' => 'Collectif',
                'birthYear' => null,
                'deathYear' => null
            ],
            [
                'name' => 'Anonyme',
                'birthYear' => null,
                'deathYear' => null
            ]
        ];

        foreach ($authors as $authorData) {
            $author = new Author();
            $author->setName($authorData['name']);
            $author->setBirthYear($authorData['birthYear']);
            $author->setDeathYear($authorData['deathYear']);
            
            $manager->persist($author);
            // Référence ASCII cohérente
            $asciiRef = 'author_' . $this->slugify($authorData['name']);
            $this->addReference($asciiRef, $author);
            // Référence historique (avec accents) si différente
            $legacyRef = 'author_' . strtolower(str_replace(' ', '_', $authorData['name']));
            if ($legacyRef !== $asciiRef) {
                $this->addReference($legacyRef, $author);
            }
        }

        $manager->flush();
    }
}
