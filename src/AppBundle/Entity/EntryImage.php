<?php

namespace AppBundle\Entity;

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

    /**
     * @return Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }
}
