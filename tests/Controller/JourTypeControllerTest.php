<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JourTypeControllerTest extends WebTestCase
{
    public function testIndexRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/jour-type');

        $this->assertResponseRedirects('/login');
    }

    public function testIndexPageLoadsForAuthenticatedUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@linott.local']);

        $client->loginUser($user);
        $client->request('GET', '/jour-type');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mes modeles de journee');
    }

    public function testNewPageLoadsForAuthenticatedUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@linott.local']);

        $client->loginUser($user);
        $client->request('GET', '/jour-type/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nouveau modele de journee');
    }

    public function testCreateJourType(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user@linott.local']);

        $client->loginUser($user);
        $crawler = $client->request('GET', '/jour-type/new');

        $form = $crawler->selectButton('Creer le modele')->form([
            'jour_type[nom]' => 'Mon nouveau modele test',
            'jour_type[description]' => 'Description du modele de test',
            'jour_type[ordre]' => '10',
            'jour_type[actif]' => true,
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/jour-type');
        $client->followRedirect();
        $this->assertSelectorTextContains('.bg-green-50', 'Mon nouveau modele test');
    }
}
