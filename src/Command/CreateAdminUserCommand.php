<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates an admin user if it does not already exist.',
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check if the admin user already exists
        $adminUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => 'admin@example.com']);

        if (!$adminUser) {
            // Create a new admin user
            $adminUser = new Utilisateur();
            $adminUser->setNom('Admin');
            $adminUser->setPrenom('User');
            $adminUser->setEmail('admin@example.com');
            $adminUser->setTelephone('1234567890');
            $adminUser->setAdresse('Admin Address');
            $adminUser->setRole(Role::ADMIN);

            // Hash the password
            $hashedPassword = $this->passwordHasher->hashPassword($adminUser, '123456');
            $adminUser->setMotdepasse($hashedPassword);

            // Save the admin user to the database
            $this->entityManager->persist($adminUser);
            $this->entityManager->flush();

            $output->writeln('Admin user created successfully!');
        } else {
            $output->writeln('Admin user already exists.');
        }

        return Command::SUCCESS;
    }
}