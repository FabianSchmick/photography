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
class TourFile extends File
{
    /**
     * @Vich\UploadableField(mapping="tour_file", fileNameProperty="fileName", mimeType="mimeType", originalName="originalName")
     */
    private ?BaseFile $file;

    /**
     * @ORM\OneToOne(targetEntity="Tour", mappedBy="file")
     */
    private Tour $tour;

    public function getTour(): Tour
    {
        return $this->tour;
    }

    public function setTour(Tour $tour): void
    {
        $this->tour = $tour;
    }
}
