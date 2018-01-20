<?php

namespace AppBundle\Service;

use AppBundle\Entity\Entry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class EntryService
{
    /**
     * Entity Manager
     *
     * @var EntityManager $em
     */
    private $em;

    /**
     * Core service
     *
     * @var CoreService $coreService
     */
    private $coreService;

    /**
     * Author service
     *
     * @var AuthorService $authorService
     */
    private $authorService;

    /**
     * Location service
     *
     * @var LocationService $locationService
     */
    private $locationService;

    /**
     * Tag service
     *
     * @var TagService $tagService
     */
    private $tagService;


    /**
     * EntryService constructor.
     *
     * @param   EntityManager     $em               Entity Manager
     * @param   CoreService       $coreService      Core service
     * @param   AuthorService     $authorService    Author service
     * @param   LocationService   $locationService  Location service
     * @param   TagService        $tagService       Tag service
     */
    public function __construct(EntityManager $em, CoreService $coreService, AuthorService $authorService, LocationService $locationService, TagService $tagService)
    {
        $this->em = $em;
        $this->coreService = $coreService;
        $this->authorService = $authorService;
        $this->locationService = $locationService;
        $this->tagService = $tagService;
    }

    /**
     * Save an entry
     *
     * @param   array   $entry              Array of data for saving an entry object
     * @param   array|UploadedFile $image   Empty array or UploadFile object with containing image
     *
     * @return  Entry   $entryEntity        The saved entry entity
     */
    public function saveEntry(array $entry, $image)
    {
        $imageName = '';
        $entryEntity = new Entry();
        if (isset($entry['id'])) {
            $entryEntity = $this->em->getRepository('AppBundle:Entry')->findOneBy(['id' => $entry['id']]);
            $imageName = $entryEntity->getImage();
        }

        $entryEntity->setTitle($entry['title']);
        $entryEntity->setDescription($entry['description']);

        if (!empty($entry['author'])) {
            $authorEntity = $this->authorService->saveAuthor(['name' => $entry['author']]);

            $this->em->persist($authorEntity);

            $entryEntity->setAuthor($authorEntity);
        }

        if (isset($image['image'])) {
            $imageName = $this->coreService->saveImage($image, true);
        }

        $entryEntity->setImage($imageName);

        if (!empty($entry['location'])) {
            $locationEntity = $this->locationService->saveLocation(['name' => $entry['location']]);

            $this->em->persist($locationEntity);

            $entryEntity->setLocation($locationEntity);
        }

        $entryEntity->setTimestamp(new \DateTime("now"));
        if ($timestamp = date_create(date($entry['timestamp']))) {
            $entryEntity->setTimestamp($timestamp);
        }

        $tagsArrayCollection = new ArrayCollection();
        foreach ($entry['tags'] as $tag) {
            $tagEntity = $this->tagService->saveTag(['name' => $tag]);

            $this->em->persist($tagEntity);

            $tagsArrayCollection->add($tagEntity);
        }
        $entryEntity->setTags($tagsArrayCollection);

        $this->em->persist($entryEntity);
        $this->em->flush();

        return $entryEntity;
    }
}
