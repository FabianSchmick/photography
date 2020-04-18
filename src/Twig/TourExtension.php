<?php

namespace App\Twig;

use App\Entity\Tour;
use App\Service\TourService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TourExtension extends AbstractExtension
{
    /**
     * @var TourService
     */
    private $tourService;

    /**
     * TourExtension constructor.
     */
    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    /**
     * Register Twig filter.
     *
     * @return \Twig\TwigFilter[]
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
    public function formatDuration(?\DateTime $duration): ?string
    {
        return $this->tourService->formatDuration($duration);
    }

    public function getName(): string
    {
        return 'tour';
    }
}
