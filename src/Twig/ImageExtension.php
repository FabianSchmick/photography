<?php

namespace App\Twig;

use Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $publicDir;

    /**
     * ImageExtension constructor.
     */
    public function __construct(string $publicDir)
    {
        $this->publicDir = $publicDir;
    }

    /**
     * Register Twig filter.
     *
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
     * @throws Exception
     */
    public function getImageDimensions(string $filename): array
    {
        list($width, $height) = getimagesize($this->publicDir.$filename);

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    public function getName(): string
    {
        return 'image';
    }
}
