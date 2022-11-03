<?php

namespace App\Service;

use App\Entity\Tour;
use phpGPX\Models\Track;
use phpGPX\phpGPX;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TourService
{
    /**
     * TourService constructor.
     */
    public function __construct(private readonly UploaderHelper $uploaderHelper, private readonly string $publicDir)
    {
    }

    /**
     * Returns the elevation data for an elevation chart.
     */
    public function getElevationData(Tour $tour): array
    {
        if (empty($tour->getSegments()[0])) {
            return [];
        }

        $elevationData = [];
        foreach ($tour->getSegments()[0]->points as $point) {
            $elevationData[] = [
                (int) $point->distance / 1000, (int) $point->elevation,
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
        $tour->setDistance($tour->getDistance() ?? (int) $track->stats->distance);
        $tour->setMinAltitude($tour->getMinAltitude() ?? (int) $track->stats->minAltitude);
        $tour->setMaxAltitude($tour->getMaxAltitude() ?? (int) $track->stats->maxAltitude);
        $tour->setCumulativeElevationGain($tour->getCumulativeElevationGain() ?? (int) $track->stats->cumulativeElevationGain);
        $tour->setCumulativeElevationLoss($tour->getCumulativeElevationLoss() ?? (int) $track->stats->cumulativeElevationLoss);
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
    public function calcTourDuration(Tour $tour): ?\DateInterval
    {
        if (!($formulaType = $tour->getFormulaType()) || !$tour->getCumulativeElevationGain()) {
            return null;
        }

        return match ($formulaType) {
            'HIKING' => $this->calcHikingDuration($tour->getDistance(), $tour->getCumulativeElevationGain(), $tour->getCumulativeElevationLoss()),
            'MTB' => $this->calcMountainBikeDuration($tour->getDistance(), $tour->getCumulativeElevationGain()),
            'VIA_FERRATA' => $this->calcViaFerrataDuration($tour->getCumulativeElevationGain(), $tour->getCumulativeElevationLoss()),
            default => null,
        };
    }

    /**
     * Calc the hiking tour duration.
     */
    public function calcHikingDuration($distance, $upElevation, $downElevation): \DateInterval
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
    public function calcMountainBikeDuration($distance, $upElevation): \DateInterval
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
    public function calcViaFerrataDuration($upElevation, $downElevation): \DateInterval
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
    public function formatDuration(?\DateInterval $duration): ?string
    {
        if ($duration === null) {
            return null;
        }

        return $duration->format('%h:%I');
    }

    /**
     * Calculate the real time from a decimal duration.
     */
    private function formatDecimalDuration($decimalDuration): \DateInterval
    {
        $hours = floor($decimalDuration);
        $decimalMinutes = ($hours - $decimalDuration) * -1;

        $minutes = (int) ($decimalMinutes * (60 / 1)); // 60 minutes/hour

        return new \DateInterval("PT{$hours}H{$minutes}M");
    }
}
