<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/de/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Fotografie', $crawler->filter('#introduction h1')->text());

        $crawler = $client->request('GET', '/en/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Photography', $crawler->filter('#introduction h1')->text());
    }
}
