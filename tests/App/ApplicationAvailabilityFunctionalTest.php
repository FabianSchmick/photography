<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define a functional test that at least checks if your application pages are successfully loading.
 * https://symfony.com/doc/4.4/best_practices.html#tests
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
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
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
        yield ['/de/tour/page/1'];
        yield ['/en/tour/page/1'];
        yield ['/sitemap.xml'];
    }

    /**
     * Secure pages
     */
    public function getSecureUrls()
    {
        yield ['/admin/'];
        yield ['/admin/entry/new'];
        yield ['/admin/language/en'];
    }
}
