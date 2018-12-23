<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="file")
     */
    private $tour;

    /**
     * @return Tour
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param Tour $tour
     */
    public function setTour(Tour $tour)
    {
        $this->tour = $tour;
    }
}
