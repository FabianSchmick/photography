<?php

namespace AppBundle\Service;

use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class TagService
{
    /**
     * Entity Manager
     *
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * Core service
     *
     * @var CoreService $coreService
     */
    private $coreService;


    /**
     * TagService constructor.
     *
     * @param   EntityManagerInterface  $em               Entity Manager
     * @param   CoreService             $coreService      Core service
     */
    public function __construct(EntityManagerInterface $em, CoreService $coreService)
    {
        $this->em = $em;
        $this->coreService = $coreService;
    }

    /**
     * Save a tag
     *
     * @param   array               $tag              Array of data for saving a tag object
     * @param   array|UploadedFile  $image            Empty array or UploadFile object with containing image
     *
     * @return  Tag                 $tagEntity        The saved tag entity
     */
    public function saveTag(array $tag, $image = [])
    {
        $imageName = '';
        $tagEntity = new Tag();
        if (isset($tag['id'])) {
            $tagEntity = $this->em->getRepository('AppBundle:Tag')->findOneBy(['id' => $tag['id']]);
            $imageName = $tagEntity->getImage();
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

        if (!empty($tag['deleteImage'])) {
            $this->coreService->deleteImage($tagEntity->getImage());

            $imageName = '';

            $tagEntity->setImage($imageName);
        }

        if (!empty($image['image'])) {
            if ($tagEntity->getImage()) {
                $this->coreService->deleteImage($tagEntity->getImage());
            }

            $imageName = $this->coreService->saveImage($image);
        }

        $tagEntity->setImage($imageName);

        $this->em->persist($tagEntity);
        $this->em->flush();

        return $tagEntity;
    }
}
