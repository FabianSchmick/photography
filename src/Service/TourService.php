<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourFile;
use App\Repository\TourRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpGPX\Models\Point;
use phpGPX\Models\Track;
use phpGPX\phpGPX;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TourService
{
    /**
     * Entity Manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TourRepository
     */
    private $tourRepository;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * TourService constructor.
     *
     * @param EntityManagerInterface $em Entity Manager
     */
    public function __construct(EntityManagerInterface $em, TourRepository $tourRepository, UploaderHelper $uploaderHelper, $publicDir)
    {
        $this->em = $em;
        $this->tourRepository = $tourRepository;
        $this->uploaderHelper = $uploaderHelper;
        $this->publicDir = $publicDir;
    }

    /**
     * Save an tour.
     *
     * @param array $tour Array of data for saving an tour object
     * @param File  $file UploadFile object with containing gpx file
     *
     * @return Tour $tourEntity     The saved tour entity
     */
    public function saveTour(array $tour, File $file = null): Tour
    {
        $duplicate = $this->tourRepository->findOneBy(['name' => $tour['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $tourEntity = new Tour();
        if (isset($tour['id'])) {
            $tourEntity = $this->tourRepository->findOneBy(['id' => $tour['id']]);
        }
        $tourEntity->setName($tour['name']);
        $tourEntity->setDescription($tour['description']);

        if ($file) {
            $tourFile = new TourFile();
            $tourFile->setFile($file);

            $tourEntity->setFile($tourFile);
        }

        $this->em->persist($tourEntity);
        $this->em->flush();

        return $tourEntity;
    }

    /**
     * Sets the stats data for a track from the gpx file.
     */
    public function setGpxData(Tour &$tour, ?Track $track = null): void
    {
        if ($track === null) {
            $track = $this->getGpxData($tour);
        }

        $tour->setDescription($tour->getDescription() ?? $track->description);
        $tour->setDistance($tour->getDistance() ?? $track->stats->distance);
        $tour->setMinAltitude($tour->getMinAltitude() ?? $track->stats->minAltitude);
        $tour->setMaxAltitude($tour->getMaxAltitude() ?? $track->stats->maxAltitude);
        $tour->setCumulativeElevationGain($tour->getCumulativeElevationGain() ?? $track->stats->cumulativeElevationGain);
        $tour->setSegments($track->segments);
        $tour->setDuration($tour->getDuration() ?? $this->calcTourDuration($tour)); // Calc last, so all needed values are set
    }

    /**
     * Read the stats data from a tour gpx file.
     */
    public function getGpxData(Tour $tour): Track
    {
        $gpx = new phpGPX();

        $file = $gpx->load($this->publicDir.$this->uploaderHelper->asset($tour->getFile(), 'file'));

        $firstTrack = reset($file->tracks);
        $firstTrack->stats->distance = $firstTrack->stats->distance / 1000;

        return $firstTrack;
    }

    /**
     * Calc the tour duration with DIN 33466.
     */
    public function calcTourDuration(Tour $tour): ?\DateTime
    {
        if (!$segments = $tour->getSegments()) {
            return null;
        }

        /** @var Point[] $points */
        $points = $segments[0]->points;
        $count = count($points);

        $upElevation = 0;
        $downElevation = 0;
        for ($i = 1; $i < $count; ++$i) {
            $last = $points[$i - 1]->elevation;
            $current = $points[$i]->elevation;

            if ($last > $current) {
                $downElevation += $last - $current;
            } else {
                $upElevation += $current - $last;
            }
        }

        $downDuration = $downElevation / Tour::DOWN_METERS_PER_HOUR;
        $upDuration = $upElevation / Tour::UP_METERS_PER_HOUR;
        $sumElevation = $downDuration + $upDuration;
        $horizontalDuration = $tour->getDistance() / Tour::HORIZONTAL_METERS_PER_HOUR;

        if ($horizontalDuration < $sumElevation) {
            $decimalDuration = ($horizontalDuration / 2) + $sumElevation;
        } else {
            $decimalDuration = ($sumElevation / 2) + $horizontalDuration;
        }

        // Calc the real time now
        $hours = floor($decimalDuration);
        $decimalMinutes = ($hours - $decimalDuration) * -1;

        $minutes = $decimalMinutes * (60 / 1); // 60 minutes/hour

        $time = new \DateTime();
        $time->setTime((int) $hours, (int) $minutes);

        return $time;
    }

    /**
     * Formats the tour duration.
     * Removes leading zero from hours.
     */
    public function formatDuration(?\DateTime $duration): ?string
    {
        if ($duration === null) {
            return null;
        }

        return ltrim($duration->format('H'), 0).':'.$duration->format('i');
    }
}
