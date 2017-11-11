<?php

namespace AppBundle\Service;

use AppBundle\Entity\Location;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;


class LocationService
{
    /**
     * Entity Manager
     *
     * @var EntityManager $em
     */
    private $em;


    /**
     * LocationService constructor.
     *
     * @param   EntityManager     $em           Entity Manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Save a location
     *
     * @param   array     $location         Array of data for saving a location object
     *
     * @return  Location  $locationEntity   The saved location entity
     */
    public function saveLocation(array $location)
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
