<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\EntryService;
use AppBundle\Service\TourService;
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

    /**
     * @var TourService
     */
    private $tourService;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(EntryService $entryService, TourService $tourService, $projectDir)
    {
        $this->entryService = $entryService;
        $this->tourService = $tourService;
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $tours = [
            [
                'name' => 'Tour to the top of the mountain',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
            ],
        ];

        $entries = [
            [
                'name' => 'Forest near my hometown',
                'description' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'location' => 'Germany, NRW',
                'timestamp' => '2017-10-26',
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
                'timestamp' => '2017-10-27',
                'tags' => [
                    'Nature', 'Mountain', 'Landscape',
                ],
            ],
            [
                'name' => 'Field with mushrooms',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'location' => 'Germany, NRW',
                'timestamp' => '2017-10-28',
                'tags' => [
                    'Nature', 'Grass', 'Landscape',
                ],
            ],
            [
                'name' => 'Sunset in winter',
                'description' => 'Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'author' => 'Fabian Schmick',
                'location' => 'Germany, NRW',
                'timestamp' => '2017-10-29',
                'tags' => [
                    'Nature', 'Sunset', 'Winter', 'Snow', 'Landscape',
                ],
            ],
        ];

        $imgFixturesDir = $this->projectDir.'/fixtures/img';
        $tourFixturesDir = $this->projectDir.'/fixtures/tour';
        $tmpDir = __DIR__.'/../tmp';

        $fileSystem = new Filesystem();
        $fileSystem->remove($tmpDir);
        $fileSystem->mirror($tourFixturesDir, $tmpDir);

        $gpxFiles = $this->getFiles($tmpDir);

        foreach ($tours as $tour) {
            $this->tourService->saveTour($tour, $gpxFiles[array_rand($gpxFiles)]);
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove($tmpDir);
        $fileSystem->mirror($imgFixturesDir, $tmpDir);

        $images = $this->getFiles($tmpDir);

        // 20 entries
        for ($i = 0; $i < 5; ++$i) {
            foreach ($entries as $key => $entry) {
                $minute = (string) random_int(10, 59);
                $entry['timestamp'] .= ' 11:'.$minute;
                $this->entryService->saveEntry($entry, $images[$key]);
            }
            if ($i < 4) {
                $fileSystem->mirror($imgFixturesDir, $tmpDir);
            }
            shuffle($images);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile[]
     */
    private function getFiles($tmpDir): array
    {
        $finder = new Finder();
        $finder->files()->in($tmpDir);

        foreach ($finder as $file) {
            $files[] = new UploadedFile($file->getRealPath(), $file->getFilename(), null, null, null, true);
        }

        return $files ?? [];
    }
}
