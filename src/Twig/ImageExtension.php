<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageExtension extends AbstractExtension
{
    public function __construct(private readonly string $publicDir)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('image_dimensions', [$this, 'getImageDimensions']),
        ];
    }

    /**
     * Returns the image dimensions (width and height) for a given imagepath.
     *
     * @param string $filename Imagepath
     *
     * @throws \Exception
     */
    public function getImageDimensions(string $filename): array
    {
        [$width, $height] = getimagesize($this->publicDir.$filename);

        return [
            'width' => $width,
            'height' => $height,
        ];
    }
}
