<?php

namespace App\Config;

enum TourDurationFormula
{
    case HIKING;
    case MTB;
    case VIA_FERRATA;

    public function constants(): array
    {
        return match ($this) {
            TourDurationFormula::HIKING => [
                /* Values according to: DIN 33466 */
                'UP_METERS_PER_HOUR' => 300,
                'DOWN_METERS_PER_HOUR' => 500,
                'HORIZONTAL_KILOMETERS_PER_HOUR' => 4,
            ],
            TourDurationFormula::MTB => [
                'UP_METERS_PER_HOUR' => 500,
                'HORIZONTAL_KILOMETERS_PER_HOUR' => 12,
            ],
            TourDurationFormula::VIA_FERRATA => [
                'UP_METERS_PER_HOUR' => 200,
                'DOWN_METERS_PER_HOUR' => 400,
            ],
        };
    }
}
