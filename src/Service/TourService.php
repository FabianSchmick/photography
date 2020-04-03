<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\TourFile;
use App\Repository\TourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class TourService
{
    /**
     * Entity Manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TourRepository
     */
    private $tourRepository;

    /**
     * TourService constructor.
     *
     * @param EntityManagerInterface $em Entity Manager
     */
    public function __construct(EntityManagerInterface $em, TourRepository $tourRepository)
    {
        $this->em = $em;
        $this->tourRepository = $tourRepository;
    }

    /**
     * Save an tour.
     *
     * @param array $tour Array of data for saving an tour object
     * @param File  $file UploadFile object with containing gpx file
     *
     * @return Tour $tourEntity     The saved tour entity
     */
    public function saveTour(array $tour, File $file = null): Tour
    {
        $duplicate = $this->tourRepository->findOneBy(['name' => $tour['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $tourEntity = new Tour();
        if (isset($tour['id'])) {
            $tourEntity = $this->tourRepository->findOneBy(['id' => $tour['id']]);
        }
        $tourEntity->setName($tour['name']);
        $tourEntity->setDescription($tour['description']);

        if ($file) {
            $tourFile = new TourFile();
            $tourFile->setFile($file);

            $tourEntity->setFile($tourFile);
        }

        $this->em->persist($tourEntity);
        $this->em->flush();

        return $tourEntity;
    }
}
