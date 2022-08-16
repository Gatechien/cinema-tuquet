<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');
    }

    public function testBack(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/back/movie');

        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }
}
