<?php

namespace App\Twig;

use App\Entity\Tour;
use App\Service\TourService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TourExtension extends AbstractExtension
{
    public function __construct(private readonly TourService $tourService)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_duration', [$this, 'formatDuration']),
        ];
    }

    /**
     * Formats the tour duration.
     */
    public function formatDuration(?\DateInterval $duration): ?string
    {
        return $this->tourService->formatDuration($duration);
    }
}
