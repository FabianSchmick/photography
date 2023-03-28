<?php

namespace App\Tests\Builder;

use App\Entity\Post;
use App\Entity\File;
use App\Entity\Location;
use App\Entity\Tour;
use App\Entity\TourCategory;
use App\Entity\TourFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TourBuilder
{
    private array $names = [];

    private array $descriptions = [];

    private ?string $directions = null;

    private ?string $equipmentAndSafety = null;

    private ?float $distance = null;

    private ?\DateInterval $duration = null;

    private ?int $maxAltitude = null;

    private ?int $minAltitude = null;

    private ?int $cumulativeElevationGain = null;

    private ?int $cumulativeElevationLoss = null;

    private ?int $levelOfDifficulty = null;

    private ?int $sort = null;

    /**
     * @var Collection<Location>
     */
    private Collection $locations;

    private ?Post $previewPost = null;

    private File $file;

    private ?TourCategory $tourCategory = null;

    public function __construct(private readonly ObjectManager $manager)
    {
        $this->locations = new ArrayCollection();

        $this->file = new TourFile();
        $this->file->setFile(
            new UploadedFile(__DIR__.'/../../fixtures/tour/winterberg-kahler-asten-steig.gpx', 'winterberg-kahler-asten-steig.gpx', 'text/xml', null, true)
        );
    }

    public function create(): Tour
    {
        $tour = new Tour();
        $tour->setDirections($this->directions);
        $tour->setEquipmentAndSafety($this->equipmentAndSafety);
        $tour->setDistance($this->distance);
        $tour->setDuration($this->duration);
        $tour->setMaxAltitude($this->maxAltitude);
        $tour->setMinAltitude($this->minAltitude);
        $tour->setCumulativeElevationGain($this->cumulativeElevationGain);
        $tour->setCumulativeElevationLoss($this->cumulativeElevationLoss);
        $tour->setLevelOfDifficulty($this->levelOfDifficulty);
        $tour->setSort($this->sort);
        $tour->setLocations($this->locations);
        $tour->setPreviewPost($this->previewPost);
        $tour->setFile($this->file);
        $tour->setTourCategory($this->tourCategory);
        $tour->setName($this->names[array_key_first($this->names)] ?? 'Tour');
        $tour->setDescription($this->descriptions[array_key_first($this->descriptions)] ?? null);

        $this->manager->persist($tour);
        $this->manager->flush();

        unset($this->names[array_key_first($this->names)]);
        foreach ($this->names as $locale => $name) {
            $tour->setName($name);
            $tour->setTranslatableLocale($locale);

            $this->manager->persist($tour);
            $this->manager->flush();
        }

        unset($this->descriptions[array_key_first($this->descriptions)]);
        foreach ($this->descriptions as $locale => $description) {
            $tour->setDescription($description);
            $tour->setTranslatableLocale($locale);

            $this->manager->persist($tour);
            $this->manager->flush();
        }

        return $tour;
    }

    public function setName(string $name, string $locale = 'en'): self
    {
        $this->names[$locale] = $name;

        return $this;
    }

    public function setDescription(?string $description, string $locale = 'en'): self
    {
        $this->descriptions[$locale] = $description;

        return $this;
    }

    public function setDirections(?string $directions): self
    {
        $this->directions = $directions;

        return $this;
    }

    public function setEquipmentAndSafety(?string $equipmentAndSafety): self
    {
        $this->equipmentAndSafety = $equipmentAndSafety;

        return $this;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function setMaxAltitude(?int $maxAltitude): self
    {
        $this->maxAltitude = $maxAltitude;

        return $this;
    }

    public function setMinAltitude(?int $minAltitude): self
    {
        $this->minAltitude = $minAltitude;

        return $this;
    }

    public function setCumulativeElevationGain(?int $cumulativeElevationGain): self
    {
        $this->cumulativeElevationGain = $cumulativeElevationGain;

        return $this;
    }

    public function setCumulativeElevationLoss(?int $cumulativeElevationLoss): self
    {
        $this->cumulativeElevationLoss = $cumulativeElevationLoss;

        return $this;
    }

    public function setLevelOfDifficulty(?int $levelOfDifficulty): self
    {
        $this->levelOfDifficulty = $levelOfDifficulty;

        return $this;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function addLocation(Location $location): self
    {
        $this->locations->add($location);

        return $this;
    }

    public function removeTag(Location $location): self
    {
        $this->locations->removeElement($location);

        return $this;
    }

    public function setPreviewPost(?Post $previewPost): self
    {
        $this->previewPost = $previewPost;

        return $this;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = new TourFile();
        $this->file->setFile($file);

        return $this;
    }

    public function setTourCategory(?TourCategory $tourCategory): self
    {
        $this->tourCategory = $tourCategory;

        return $this;
    }
}
