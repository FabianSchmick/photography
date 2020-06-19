<?php

namespace App\Form\DataTransformer;

use App\Service\TourService;
use DateInterval;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateIntervalTransformer implements DataTransformerInterface
{
    /**
     * @var TourService
     */
    private $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    /**
     * Transform a DateInterval into a string like: 9:25
     *
     * @param  mixed       $value
     * @return string|null
     */
    public function transform($value)
    {
        if ($value !== null && !$value instanceof DateInterval) {
            throw new TransformationFailedException(sprintf(
                'Can\'t transform value "%s"!',
                $value
            ));
        }

        return $this->tourService->formatDuration($value);
    }

    /**
     * Reverse transform a string like: 9:25 into a DateInterval object
     *
     * @param  mixed             $value
     * @return DateInterval|null
     * @throws \Exception
     */
    public function reverseTransform($value)
    {
        if ($value !== null && !is_string($value)) {
            throw new TransformationFailedException(sprintf(
                'Can\'t transform value "%s"!',
                $value
            ));
        }

        if (!$value) {
            return null;
        }

        $values = explode(':', $value);

        $intervalSpec = "PT{$values[0]}H";
        if (!empty($values[1])) {
            $intervalSpec .= "{$values[1]}M";
        }

        return new DateInterval($intervalSpec);
    }
}
