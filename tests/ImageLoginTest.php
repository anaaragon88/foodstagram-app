<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class ImageLoginTest extends PantherTestCase
{
    public function testSomething(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorTextContains('button', 'LOG IN');
    }
}
