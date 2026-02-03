<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);

        // Create test database schema
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate schema
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // Create test user
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setEmail('test@linott.local');
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setRoles([]);
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));
        $user->setActif(true);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function testLoginPageIsAccessible(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Linott');
    }

    public function testRedirectToLoginWhenNotAuthenticated(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }

    public function testLoginWithValidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@linott.local',
            '_password' => 'password',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tableau de bord');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@linott.local',
            '_password' => 'wrongpassword',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.bg-red-50');
    }

    public function testLogout(): void
    {
        // Login first
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@linott.local',
            '_password' => 'password',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        // Now logout
        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects('/login');
    }
}
