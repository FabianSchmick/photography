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
     * @var BaseFile
     *
     * @Vich\UploadableField(mapping="entry_image", fileNameProperty="fileName", mimeType="mimeType", originalName="originalName")
     */
    private $file;

    /**
     * @var Entry
     *
     * @ORM\OneToOne(targetEntity="Entry", mappedBy="image")
     */
    private $entry;

    public function getEntry(): Entry
    {
        return $this->entry;
    }

    public function setEntry(Entry $entry): void
    {
        $this->entry = $entry;
    }
}
