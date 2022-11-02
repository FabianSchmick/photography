<?php

namespace App\Service;

use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;

class LocationService
{
    /**
     * LocationService constructor.
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly LocationRepository $locationRepository)
    {
    }

    /**
     * Save a location.
     *
     * @param array $location Array of data for saving a location object
     *
     * @return Location $locationEntity   The saved location entity
     */
    public function saveLocation(array $location): Location
    {
        $duplicate = $this->locationRepository->findOneBy(['name' => $location['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $locationEntity = new Location();
        if (isset($location['id'])) {
            $locationEntity = $this->locationRepository->findOneBy(['id' => $location['id']]);
        }
        $locationEntity->setName($location['name']);

        $this->em->persist($locationEntity);
        $this->em->flush();

        return $locationEntity;
    }
}
