<?php

namespace AppBundle\Service;

use AppBundle\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class LocationService
{
    /**
     * Entity Manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LocationService constructor.
     *
     * @param EntityManagerInterface $em Entity Manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $duplicate = $this->em->getRepository('AppBundle:Location')->findOneBy(['name' => $location['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $locationEntity = new Location();
        if (isset($location['id'])) {
            $locationEntity = $this->em->getRepository('AppBundle:Location')->findOneBy(['id' => $location['id']]);
        }
        $locationEntity->setName($location['name']);

        $this->em->persist($locationEntity);
        $this->em->flush();

        return $locationEntity;
    }
}
