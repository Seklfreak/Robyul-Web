<?php

namespace RobyulWebBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains(
            'Robyul is a Discord Bot developed especially with the needs of of KPop Discord Servers in mind.',
            $client->getResponse()->getContent());
    }
}
