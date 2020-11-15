<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourFile;
use App\Repository\TourRepository;
use DateInterval;
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
     * Returns the elevation data for an elevation chart
     */
    public function getElevationData(Tour $tour): array
    {
        if (empty($tour->getSegments()[0])) {
            return [];
        }

        $elevationData = [];
        foreach ($tour->getSegments()[0]->points as $point) {
            $elevationData[] = [
                round($point->distance) / 1000, intval($point->elevation)
            ];
        }

        return $elevationData;
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
     * Calc the tour duration dependent on formula type.
     */
    public function calcTourDuration(Tour $tour): ?DateInterval
    {
        if (!($segments = $tour->getSegments()) || !($formulaType = $tour->getFormulaType()) || !$tour->getMaxAltitude()) {
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

        switch ($formulaType) {
            case 'HIKING':
                return $this->calcHikingDuration($tour->getDistance(), $upElevation, $downElevation);
            case 'MTB':
                return $this->calcMountainBikeDuration($tour->getDistance(), $upElevation);
            case 'VIA_FERRATA':
                return $this->calcViaFerrataDuration($upElevation, $downElevation);
        }

        return null;
    }

    /**
     * Calc the hiking tour duration.
     */
    public function calcHikingDuration($distance, $upElevation, $downElevation): DateInterval
    {
        $formulaDefinitions = Tour::FORMULA_DEFINITIONS['HIKING'];

        $downDuration = $downElevation / $formulaDefinitions['DOWN_METERS_PER_HOUR'];
        $upDuration = $upElevation / $formulaDefinitions['UP_METERS_PER_HOUR'];
        $sumElevation = $downDuration + $upDuration;
        $horizontalDuration = $distance / $formulaDefinitions['HORIZONTAL_METERS_PER_HOUR'];

        if ($horizontalDuration < $sumElevation) {
            $decimalDuration = ($horizontalDuration / 2) + $sumElevation;
        } else {
            $decimalDuration = ($sumElevation / 2) + $horizontalDuration;
        }

        return $this->formatDecimalDuration($decimalDuration);
    }

    /**
     * Calc the mountainbike tour duration.
     */
    public function calcMountainBikeDuration($distance, $upElevation): DateInterval
    {
        $formulaDefinitions = Tour::FORMULA_DEFINITIONS['MTB'];

        $upDuration = $upElevation / $formulaDefinitions['UP_METERS_PER_HOUR'];
        $horizontalDuration = $distance / $formulaDefinitions['HORIZONTAL_METERS_PER_HOUR'];

        if ($horizontalDuration < $upDuration) {
            $decimalDuration = ($horizontalDuration / 2) + $upDuration;
        } else {
            $decimalDuration = ($upDuration / 2) + $horizontalDuration;
        }

        return $this->formatDecimalDuration($decimalDuration);
    }

    /**
     * Calc the via ferrata tour duration.
     */
    public function calcViaFerrataDuration($upElevation, $downElevation): DateInterval
    {
        $formulaDefinitions = Tour::FORMULA_DEFINITIONS['VIA_FERRATA'];

        $downDuration = $downElevation / $formulaDefinitions['DOWN_METERS_PER_HOUR'];
        $upDuration = $upElevation / $formulaDefinitions['UP_METERS_PER_HOUR'];
        $sumElevation = $downDuration + $upDuration;

        return $this->formatDecimalDuration($sumElevation);
    }

    /**
     * Formats the tour duration.
     * Removes leading zero from hours.
     */
    public function formatDuration(?DateInterval $duration): ?string
    {
        if ($duration === null) {
            return null;
        }

        return $duration->format('%h:%I');
    }

    /**
     * Calculate the real time from a decimal duration.
     */
    private function formatDecimalDuration($decimalDuration): DateInterval
    {
        $hours = floor($decimalDuration);
        $decimalMinutes = ($hours - $decimalDuration) * -1;

        $minutes = (int) ($decimalMinutes * (60 / 1)); // 60 minutes/hour

        return new DateInterval("PT{$hours}H{$minutes}M");
    }
}
