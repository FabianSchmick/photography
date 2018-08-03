<?php

namespace AppBundle\Service;

use AppBundle\Entity\Entry;
use AppBundle\Entity\EntryImage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class EntryService
{
    /**
     * Entity Manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Author service.
     *
     * @var AuthorService
     */
    private $authorService;

    /**
     * Location service.
     *
     * @var LocationService
     */
    private $locationService;

    /**
     * Tag service.
     *
     * @var TagService
     */
    private $tagService;

    /**
     * EntryService constructor.
     *
     * @param EntityManagerInterface $em              Entity Manager
     * @param AuthorService          $authorService   Author service
     * @param LocationService        $locationService Location service
     * @param TagService             $tagService      Tag service
     */
    public function __construct(EntityManagerInterface $em, AuthorService $authorService, LocationService $locationService, TagService $tagService)
    {
        $this->em = $em;
        $this->authorService = $authorService;
        $this->locationService = $locationService;
        $this->tagService = $tagService;
    }

    /**
     * Save an entry.
     *
     * @param array $entry Array of data for saving an entry object
     * @param File  $image UploadFile object with containing image
     *
     * @return Entry $entryEntity        The saved entry entity
     */
    public function saveEntry(array $entry, File $image = null)
    {
        $entryEntity = new Entry();
        if (isset($entry['id'])) {
            $entryEntity = $this->em->getRepository('AppBundle:Entry')->findOneBy(['id' => $entry['id']]);
        }

        $entryEntity->setTitle($entry['title']);
        $entryEntity->setDescription($entry['description']);

        if (!empty($entry['author'])) {
            $authorEntity = $this->authorService->saveAuthor(['name' => $entry['author']]);

            $entryEntity->setAuthor($authorEntity);
        }

        if ($image) {
            $entryImage = new EntryImage();
            $entryImage->setFile($image);

            $entryEntity->setImage($entryImage);
        }

        if (!empty($entry['location'])) {
            $locationEntity = $this->locationService->saveLocation(['name' => $entry['location']]);

            $entryEntity->setLocation($locationEntity);
        }

        $entryEntity->setTimestamp(new \DateTime('now'));
        if ($timestamp = date_create(date($entry['timestamp']))) {
            $entryEntity->setTimestamp($timestamp);
        }

        $tagsArrayCollection = new ArrayCollection();
        foreach ($entry['tags'] as $tag) {
            $tagEntity = $this->tagService->saveTag(['name' => $tag]);

            $tagsArrayCollection->add($tagEntity);
        }
        $entryEntity->setTags($tagsArrayCollection);

        $this->em->persist($entryEntity);
        $this->em->flush();

        return $entryEntity;
    }
}
