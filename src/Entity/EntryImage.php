<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * File.
 *
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class EntryImage extends File
{
    /**
     * @Vich\UploadableField(mapping="entry_image", fileNameProperty="fileName", mimeType="mimeType", originalName="originalName")
     */
    private ?BaseFile $file;

    /**
     * @ORM\OneToOne(targetEntity="Entry", mappedBy="image")
     */
    private Entry $entry;

    /**
     * Stop PHP auto-rotating images based on EXIF 'orientation' data.
     *
     * @see https://stackoverflow.com/a/14989870/5947371
     */
    public function setFile(?BaseFile $file = null): void
    {
        if ($file === null) {
            return;
        }

        $image = new \Imagick($file->getPathname());
        $orientation = $image->getImageOrientation();

        switch ($orientation) {
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage('#000', 180); // rotate 180 degrees
                break;

            case \Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage('#000', 90); // rotate 90 degrees CW
                break;

            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage('#000', -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
        $image->writeImage($file->getPathname());

        parent::setFile($file);
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }

    public function setEntry(Entry $entry): void
    {
        $this->entry = $entry;
    }
}
