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
class TourFile extends File
{
    /**
     * @var BaseFile
     *
     * @Vich\UploadableField(mapping="tour_file", fileNameProperty="fileName", mimeType="mimeType", originalName="originalName")
     */
    private $file;

    /**
     * @var Tour
     *
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="file")
     */
    private $tour;

    /**
     * @return Tour
     */
    public function getTour(): Tour
    {
        return $this->tour;
    }

    /**
     * @param Tour $tour
     */
    public function setTour(Tour $tour): void
    {
        $this->tour = $tour;
    }
}
