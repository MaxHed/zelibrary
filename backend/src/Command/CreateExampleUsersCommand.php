<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-example-users',
    description: 'Crée des utilisateurs d\'exemple pour l\'application',
)]
class CreateExampleUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Cette commande crée 4 utilisateurs d\'exemple : Admin, Super Admin, Library Employee et User.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Création des 4 utilisateurs d\'exemple');
        
        $users = [
            [
                'email' => 'admin@zelibrary.com',
                'password' => 'admin123',
                'name' => 'Administrateur',
                'roles' => ['ROLE_ADMIN'],
                'type' => 'Admin'
            ],
            [
                'email' => 'superadmin@zelibrary.com',
                'password' => 'admin123',
                'name' => 'Super Administrateur',
                'roles' => ['ROLE_SUPER_ADMIN'],
                'type' => 'SuperAdmin'
            ],
            [
                'email' => 'bibliothecaire@zelibrary.com',
                'password' => 'employee123',
                'name' => 'Bibliothécaire',
                'roles' => ['ROLE_LIBRARY_EMPLOYEE'],
                'type' => 'LibraryEmployee'
            ],
            [
                'email' => 'user@example.com',
                'password' => 'user123',
                'name' => 'Utilisateur',
                'roles' => ['ROLE_USER'],
                'type' => 'User'
            ]
        ];
        
        try {
            foreach ($users as $userData) {
                $existingUser = $this->userRepository->findOneBy(['email' => $userData['email']]);
                
                if ($existingUser) {
                    $io->info(sprintf('Utilisateur %s existe déjà, ignoré', $userData['email']));
                    continue;
                }
                
                $user = new User();
                $user->setEmail($userData['email'])
                     ->setRoles($userData['roles']);
                
                $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
                $user->setPassword($hashedPassword);
                
                $this->entityManager->persist($user);
                $io->info(sprintf('Utilisateur créé : %s (%s)', $userData['email'], $userData['name']));
            }
            
            $this->entityManager->flush();
            
            $io->success('4 utilisateurs créés avec succès !');
            
            $io->section('Informations de connexion');
            $io->table(
                ['Email', 'Mot de passe', 'Type', 'Rôles'],
                array_map(function($userData) {
                    return [
                        $userData['email'],
                        $userData['password'],
                        $userData['type'],
                        implode(', ', $userData['roles'])
                    ];
                }, $users)
            );
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Erreur lors de la création des utilisateurs : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
