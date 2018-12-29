<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Define a functional test that at least checks if your application pages are successfully loading.
 * https://symfony.com/doc/3.4/best_practices/tests.html#functional-tests
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Some pages
     */
    public function urlProvider()
    {
        yield ['/de/'];
        yield ['/en/'];
        yield ['/login'];
        yield ['/en/tag/nature'];
        yield ['/de/tour/'];
        yield ['/en/tour/'];
        yield ['/sitemap.xml'];
    }
}
