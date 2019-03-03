<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\EntryService;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Fixtures extends AbstractFixture implements FixtureInterface
{
    /**
     * @var EntryService
     */
    private $entryService;

    public function __construct(EntryService $entryService)
    {
        $this->entryService = $entryService;
    }

    public function load(ObjectManager $manager)
    {
        $entries = [
            [
                'name' => 'Forest near my hometown',
                'description' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'image' => '',
                'location' => 'Germany, NRW',
                'timestamp' => '28.10.2017',
                'tags' => [
                    'Nature', 'Forest', 'Woods', 'Trees', 'Landscape',
                ],
            ],
            [
                'name' => 'Mountain over 2300m',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'image' => '',
                'location' => 'Germany, Bavaria',
                'timestamp' => '28.10.2017',
                'tags' => [
                    'Nature', 'Mountain', 'Landscape',
                ],
            ],
            [
                'name' => 'Field',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'image' => '',
                'location' => 'Germany, NRW',
                'timestamp' => '28.10.2017',
                'tags' => [
                    'Nature', 'Grass', 'Landscape',
                ],
            ],
            [
                'name' => 'Sunset in winter',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'image' => '',
                'location' => 'Germany, NRW',
                'timestamp' => '28.10.2017',
                'tags' => [
                    'Nature', 'Sunset', 'Winter', 'Snow', 'Landscape',
                ],
            ],
        ];

        $imgDir = __DIR__.'/../img';
        $tmpDir = __DIR__.'/../tmp';

        $fileSystem = new Filesystem();
        $fileSystem->mirror($imgDir, $tmpDir);

        $finder = new Finder();
        $finder->files()->in($tmpDir);

        foreach ($finder as $file) {
            $images[] = new UploadedFile($file->getRealPath(), $file->getFilename(), 'image/jpg', $file->getSize(), null, true);
        }

        // 20 entries
        for ($i = 0; $i < 5; ++$i) {
            foreach ($entries as $key => $entry) {
                $this->entryService->saveEntry($entry, $images[$key]);
            }
            if ($i < 4) {
                $fileSystem->mirror($imgDir, $tmpDir);
            }
            shuffle($images);
        }
    }
}
