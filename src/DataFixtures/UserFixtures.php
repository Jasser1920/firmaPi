<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);

        // Hash the password before saving
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);

        $user->setIsVerified(true);
        $user->setFullName('Default Admin');

        $manager->persist($user);
        $manager->flush();
    }
}
