<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testLaPageAccueilSaffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'entraider');
    }

    public function testLaPageConnexionSaffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');

        $this->assertResponseIsSuccessful();
    }
    #[\PHPUnit\Framework\Attributes\Group('db')]
    public function testLaPageAnnoncesSaffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/annonces');

        $this->assertResponseIsSuccessful();
    }

    public function testLeProfilRedirigeVersConnexionSiNonConnecte(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil');

        // Un visiteur non connecté doit être redirigé vers la page de connexion
        $this->assertResponseRedirects();
    }
}
