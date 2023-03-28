<?php

namespace App\DataFixtures;

use App\Tests\Builder\PostBuilder;
use App\Tests\Builder\LocationBuilder;
use App\Tests\Builder\TagBuilder;
use App\Tests\Builder\TourBuilder;
use App\Tests\Builder\TourCategoryBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $tags = [];
        $tagBuilder = new TagBuilder($manager);
        $tagBuilder->setName('Nature');
        $tagBuilder->setName('Natur', 'de');
        $tags[] = $tagBuilder->create();
        $tagBuilder->setName('Snow');
        $tagBuilder->setName('Schnee', 'de');
        $tags[] = $tagBuilder->create();
        $tagBuilder->setName('Landscape');
        $tagBuilder->setName('Landschaft', 'de');
        $tags[] = $tagBuilder->create();
        $tagBuilder->setName('Mountains');
        $tagBuilder->setName('Berge', 'de');
        $tags[] = $tagBuilder->create();

        $locations = [];
        $locationBuilder = new LocationBuilder($manager);
        $locationBuilder->setName('Munich');
        $locationBuilder->setName('München', 'de');
        $locations[] = $locationBuilder->create();
        $locationBuilder->setName('Berlin');
        $locations[] = $locationBuilder->create();

        $imgFixturesDir = $this->projectDir.'/fixtures/img';
        $tourFixturesDir = $this->projectDir.'/fixtures/tour';
        $tmpDir = __DIR__.'/tmp';

        $fileSystem = new Filesystem();
        $fileSystem->remove($tmpDir);
        $fileSystem->mirror($tourFixturesDir, $tmpDir);

        $gpxFiles = $this->getFiles($tmpDir);

        $tourCategoryBuilder = new TourCategoryBuilder($manager);
        $tourCategoryBuilder->setName('Hiking');
        $tourCategoryBuilder->setName('Wandern', 'de');
        $tourCategoryBuilder->setFormulaType('HIKING');
        $tourCategory = $tourCategoryBuilder->create();

        $tourBuilder = new TourBuilder($manager);
        $tourBuilder->setName('Winterberg - Kahler Asten Track');
        $tourBuilder->setName('Winterberg - Kahler Asten Steig', 'de');
        $tourBuilder->setFile($gpxFiles[array_rand($gpxFiles)]);
        $tourBuilder->addLocation($locations[0]);
        $tourBuilder->setTourCategory($tourCategory);
        $tour = $tourBuilder->create();

        $fileSystem = new Filesystem();
        $fileSystem->remove($tmpDir);
        $fileSystem->mirror($imgFixturesDir, $tmpDir);

        $images = $this->getFiles($tmpDir);

        for ($i = 0; $i < 20; ++$i) {
            $postBuilder = new PostBuilder($manager);
            $postBuilder->setName("Lorem Ipsum: $i");
            $postBuilder->setName("Lorem Ipsum DE: $i", 'de');
            $postBuilder->setLocation($locations[array_rand($locations)]);
            $postBuilder->setImage($images[array_rand($images)]);

            $randTagKeys = array_rand($tags, 2);
            $postBuilder->addTag($tags[$randTagKeys[0]]);
            $postBuilder->addTag($tags[$randTagKeys[1]]);

            $randomTimestamp = mt_rand((new \DateTime('2015-01-01'))->getTimestamp(), (new \DateTime())->getTimestamp());
            $randomDate = new \DateTime();
            $randomDate->setTimestamp($randomTimestamp);
            $postBuilder->setTimestamp($randomDate);

            if (random_int(0, 1)) {
                $postBuilder->setTour($tour);
            }

            $postBuilder->create();

            $fileSystem->mirror($imgFixturesDir, $tmpDir);
        }

        $fileSystem->remove($tmpDir);
    }

    /**
     * @return UploadedFile[]
     */
    private function getFiles($tmpDir): array
    {
        $finder = new Finder();
        $finder->files()->in($tmpDir);

        foreach ($finder as $file) {
            $files[] = new UploadedFile($file->getRealPath(), $file->getFilename(), null, null, true);
        }

        return $files ?? [];
    }
}
