<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define a functional test that at least checks if your application pages are successfully loading.
 * https://symfony.com/doc/5.4/best_practices.html#tests.
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $loader = new Loader();
        $loader->addFixture(new AppFixtures($kernel->getProjectDir()));

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        parent::setUpBeforeClass();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url): void
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Some pages.
     */
    public function urlProvider(): iterable
    {
        yield ['/en/'];
        yield ['/de/'];
        yield ['/login'];
        yield ['/en/tag/nature'];
        yield ['/de/tag/natur'];
        yield ['/en/tour/page/1'];
        yield ['/de/tour/page/1'];
        yield ['/en/tour/map'];
        yield ['/de/tour/map'];
        yield ['/en/tour/winterberg-kahler-asten-track'];
        yield ['/de/tour/winterberg-kahler-asten-steig'];
        yield ['/sitemap.xml'];
    }

    /**
     * Secure pages.
     */
    public function getSecureUrls(): iterable
    {
        yield ['/admin/'];
        yield ['/admin/entry/new'];
        yield ['/admin/tour/new'];
        yield ['/admin/language/en'];
    }
}
