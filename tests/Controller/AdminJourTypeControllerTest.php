<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminJourTypeControllerTest extends WebTestCase
{
    public function testAdminIndexRequiresAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@linott.local']);

        $client->loginUser($user);
        $client->request('GET', '/admin/jour-types');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminIndexLoadsForAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@linott.local']);

        $client->loginUser($admin);
        $client->request('GET', '/admin/jour-types');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modeles de journee partages');
    }

    public function testAdminNewPageLoadsForAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@linott.local']);

        $client->loginUser($admin);
        $client->request('GET', '/admin/jour-types/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nouveau modele partage');
    }
}
