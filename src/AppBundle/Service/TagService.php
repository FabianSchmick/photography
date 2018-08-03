<?php

namespace AppBundle\Service;

use AppBundle\Entity\Tag;
use AppBundle\Entity\TagImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class TagService
{
    /**
     * Entity Manager.
     *
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
     * @param array $tag   Array of data for saving a tag object
     * @param File  $image UploadFile object with containing image
     *
     * @return Tag $tagEntity        The saved tag entity
     */
    public function saveTag(array $tag, File $image = null)
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

        if ($image['image']) {
            $entryImage = new TagImage();
            $entryImage->setFile($image);

            $tagEntity->setImage($entryImage);
        }

        $this->em->persist($tagEntity);
        $this->em->flush();

        return $tagEntity;
    }
}
