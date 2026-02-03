<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const USER_REFERENCE = 'regular-user';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@linott.local');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'password')
        );
        $admin->setActif(true);

        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);

        // Create regular user
        $user = new User();
        $user->setEmail('user@linott.local');
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setRoles([]);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );
        $user->setActif(true);

        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user);

        $manager->flush();
    }
}
