<?php

namespace App\Service;

use App\Entity\Entry;
use App\Entity\EntryImage;
use App\Repository\EntryRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class EntryService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var TagService
     */
    private $tagService;

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * EntryService constructor.
     */
    public function __construct(EntityManagerInterface $em, LocationService $locationService, TagService $tagService, EntryRepository $entryRepository)
    {
        $this->em = $em;
        $this->locationService = $locationService;
        $this->tagService = $tagService;
        $this->entryRepository = $entryRepository;
    }

    /**
     * Save an entry.
     *
     * @param array $entry Array of data for saving an entry object
     * @param File  $image UploadFile object with containing image
     *
     * @return Entry $entryEntity        The saved entry entity
     */
    public function saveEntry(array $entry, File $image = null): Entry
    {
        $entryEntity = new Entry();
        if (isset($entry['id'])) {
            $entryEntity = $this->entryRepository->findOneBy(['id' => $entry['id']]);
        }

        $entryEntity->setName($entry['name']);
        $entryEntity->setDescription($entry['description']);

        if ($image) {
            $entryImage = new EntryImage();
            $entryImage->setFile($image);

            $entryEntity->setImage($entryImage);
        }

        if (!empty($entry['location'])) {
            $locationEntity = $this->locationService->saveLocation(['name' => $entry['location']]);

            $entryEntity->setLocation($locationEntity);
        }

        $entryEntity->setTimestamp(new DateTime('now'));
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
