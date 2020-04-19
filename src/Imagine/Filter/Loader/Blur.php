<?php

namespace App\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class Blur implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $image->effects()->blur($options['sigma']);

        return $image;
    }
}
