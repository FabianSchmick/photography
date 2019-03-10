<?php

namespace AppBundle\Service;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class TagService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TagService constructor.
     *
     * @param EntityManagerInterface $em Entity Manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Save a tag.
     *
     * @param array $tag Array of data for saving a tag object
     *
     * @return Tag $tagEntity        The saved tag entity
     */
    public function saveTag(array $tag)
    {
        $tagEntity = new Tag();
        if (isset($tag['id'])) {
            $tagEntity = $this->em->getRepository('AppBundle:Tag')->findOneBy(['id' => $tag['id']]);
        } else {
            $duplicate = $this->em->getRepository('AppBundle:Tag')->findOneByCriteria(['name' => $tag['name']]);
        }

        if (!empty($duplicate)) {
            return $duplicate;
        }

        $tagEntity->setName($tag['name']);

        if (!empty($tag['description'])) {
            $tagEntity->setDescription($tag['description']);
        }

        $this->em->persist($tagEntity);
        $this->em->flush();

        return $tagEntity;
    }
}
