<?php

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;


class AuthorService
{
    /**
     * Entity Manager
     *
     * @var EntityManager $em
     */
    private $em;


    /**
     * AuthorService constructor.
     *
     * @param   EntityManager     $em           Entity Manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Save an author
     *
     * @param   array   $author           Array of data for saving an author object
     *
     * @return  Author  $authorEntity     The saved author entity
     */
    public function saveAuthor(array $author)
    {
        $duplicate = $this->em->getRepository('AppBundle:Author')->findOneBy(['name' => $author['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $authorEntity = new Author();
        if (isset($author['id'])) {
            $authorEntity = $this->em->getRepository('AppBundle:Author')->findOneBy(['id' => $author['id']]);
        }
        $authorEntity->setName($author['name']);

        $this->em->persist($authorEntity);
        $this->em->flush();

        return $authorEntity;
    }
}
