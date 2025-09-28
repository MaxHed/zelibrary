<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Library;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// ALL USERS PASSWORD IS "password"

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Utilisateurs normaux
        $users = [
            ['email' => 'jean.dupont@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'marie.martin@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'pierre.durand@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'sophie.bernard@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'lucas.petit@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'emma.roux@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'thomas.moreau@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'lea.simon@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'hugo.laurent@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'chloe.david@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'alexandre.robert@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'julie.richard@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'antoine.thomas@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'camille.petit@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'maxime.michaud@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'laura.garcia@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'quentin.dubois@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'manon.moreau@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'nathan.simon@email.com', 'roles' => ['ROLE_USER']],
            ['email' => 'oceane.laurent@email.com', 'roles' => ['ROLE_USER']],
        ];

        // Bibliothécaires
        $librarians = [
            ['email' => 'bibliothecaire1@bnf.fr', 'roles' => ['ROLE_LIBRARY_EMPLOYEE']],
            ['email' => 'bibliothecaire2@bnf.fr', 'roles' => ['ROLE_LIBRARY_EMPLOYEE']],
            ['email' => 'bibliothecaire3@sorbonne.fr', 'roles' => ['ROLE_LIBRARY_EMPLOYEE']],
            ['email' => 'bibliothecaire4@ens.fr', 'roles' => ['ROLE_LIBRARY_EMPLOYEE']],
            ['email' => 'bibliothecaire5@paris.fr', 'roles' => ['ROLE_LIBRARY_EMPLOYEE']],
        ];

        // Administrateurs
        $admins = [
            ['email' => 'admin@zelibrary.com', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'superadmin@zelibrary.com', 'roles' => ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']],
        ];

        // Créer les utilisateurs normaux
        foreach ($users as $userData) {
            $user = $this->createUser($userData['email'], $userData['roles']);
            $manager->persist($user);
            $this->addReference('user_' . str_replace('@', '_at_', $userData['email']), $user);
        }

        // Créer les bibliothécaires
        foreach ($librarians as $index => $userData) {
            $user = $this->createUser($userData['email'], $userData['roles']);
            
            // Assigner une bibliothèque aléatoire
            $libraryRefs = ['library_bibliotheque_nationale_de_france', 'library_bibliotheque_sainte_genevieve', 'library_bibliotheque_mazarine'];
            $libraryRef = $libraryRefs[$index % count($libraryRefs)];
            if ($this->hasReference($libraryRef, LibraryFixtures::class)) {
                $library = $this->getReference($libraryRef, LibraryFixtures::class);
                $user->setEmployedAt($library);
            }
            
            $manager->persist($user);
            $this->addReference('librarian_' . str_replace('@', '_at_', $userData['email']), $user);
        }

        // Créer les administrateurs
        foreach ($admins as $userData) {
            $user = $this->createUser($userData['email'], $userData['roles']);
            $manager->persist($user);
            $this->addReference('admin_' . str_replace('@', '_at_', $userData['email']), $user);
        }

        $manager->flush();
    }

    private function createUser(string $email, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        
        // Mot de passe par défaut : "password"
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        
        return $user;
    }

    public function getDependencies(): array
    {
        return [
            LibraryFixtures::class,
        ];
    }
}
