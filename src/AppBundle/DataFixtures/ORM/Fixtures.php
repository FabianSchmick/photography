<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Fixtures extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $entryService = $this->container->get('AppBundle\Service\EntryService');
        $imageDir = $this->container->getParameter('image_directory');

        $entries = [
            [
                'title'         => 'Forest near my hometown',
                'description'   => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author'        => 'Fabian Schmick',
                'image'         => '',
                'location'      => 'Germany, NRW',
                'timestamp'     => '28.10.2017',
                'tags'          => [
                    'Nature', 'Forest', 'Woods', 'Trees', 'Landscape',
                ]
            ],
            [
                'title'         => 'Mountain over 2300m',
                'description'   => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author'        => 'Fabian Schmick',
                'image'         => '',
                'location'      => 'Germany, Bavaria',
                'timestamp'     => '28.10.2017',
                'tags'          => [
                    'Nature', 'Mountain', 'Landscape',
                ]
            ],
            [
                'title'         => 'Field',
                'description'   => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author'        => 'Fabian Schmick',
                'image'         => '',
                'location'      => 'Germany, NRW',
                'timestamp'     => '28.10.2017',
                'tags'          => [
                    'Nature', 'Grass', 'Landscape',
                ]
            ],
            [
                'title'         => 'Sunset in winter',
                'description'   => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author'        => 'Fabian Schmick',
                'image'         => '',
                'location'      => 'Germany, NRW',
                'timestamp'     => '28.10.2017',
                'tags'          => [
                    'Nature', 'Sunset', 'Winter', 'Snow', 'Landscape',
                ]
            ],
        ];

        $images = [
            [
                'image' => new UploadedFile(__DIR__ . '/../img/example1.jpg', 'example1', 'image/jpg'),
            ],
            [
                'image' => new UploadedFile(__DIR__ . '/../img/example2.jpg', 'example2', 'image/jpg'),
            ],
            [
                'image' => new UploadedFile(__DIR__ . '/../img/example3.jpg', 'example3', 'image/jpg'),
            ],
            [
                'image' => new UploadedFile(__DIR__ . '/../img/example4.jpg', 'example4', 'image/jpg'),
            ],
        ];

        // Delete old images
        $this->recursiveDelete($imageDir);

        // 20 entries
        for ($i = 0; $i < 5; $i++) {
            foreach ($entries as $key => $entry) {
                $entryService->saveEntry($entry, $images[$key]);
            }
            shuffle($images);
        }
    }

    function recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        } elseif (is_dir($str)) {
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index => $path) {
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

}
