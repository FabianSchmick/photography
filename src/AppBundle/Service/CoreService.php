<?php

namespace AppBundle\Service;

use Imagine\Image\Box;
use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CoreService
{
    /**
     * Directory for uploaded images
     *
     * @var string $imageDir
     */
    private $imageDir;

    /**
     * The quality for jpeg images
     *
     * @var int $imageQuality
     */
    private $imageQuality;

    /**
     * The height for thumbnails
     *
     * @var int $thumbHeight
     */
    private $thumbHeight;


    /**
     * Core constructor.
     *
     * @param   string            $imageDir         Directory for uploaded images
     * @param   int               $imageQuality     The quality for jpeg images
     * @param   int               $thumbHeight      The height for thumbnails
     */
    public function __construct($imageDir, $imageQuality = 75, $thumbHeight = 500)
    {
        $this->imageDir = $imageDir;
        $this->imageQuality = $imageQuality;
        $this->thumbHeight = $thumbHeight;
    }

    /**
     * Save images
     *
     * @param   array|UploadedFile      $image          Empty array or UploadFile object with containing image
     * @param   bool                    $thumbnail      Generate a thumbnail
     *
     * @return string                   $imageName      The image name
     */
    public function saveImage($image, $thumbnail = false)
    {
        $imageName = '';

        if (isset($image['image'])) {
            if (!file_exists($this->imageDir. '/thumb')) {    // Create image and thumbnail directory
                mkdir($this->imageDir . '/thumb', 0777, true);
            }

            // Random name and convert to jpg later
            $imageName = md5(uniqid()) . '.jpg';

            $imagine = new Imagine();
            $newImage = $imagine->open($image['image']->getPathName());
            $newImage->strip();
            $newImage->save($this->imageDir . '/' . $imageName, array('jpeg_quality' => $this->imageQuality));  // Save and minify image

            if ($thumbnail) {
                $this->saveThumbnail($newImage, $imageName);
            }
        }

        return $imageName;
    }

    /**
     * Saves a thumbnail from a existing image
     *
     * @param   Image     $image        The image to generate a thumbnail from
     * @param   string    $imageName    The image name
     */
    protected function saveThumbnail(Image $image, $imageName)
    {
        /** @var Box $size */
        $size = $image->getSize();

        $image
            ->thumbnail($size->heighten($this->thumbHeight))
            ->save($this->imageDir . '/thumb/' . $imageName, array('jpeg_quality' => $this->imageQuality))  // Save and minfiy thumbnail
        ;
    }
}
