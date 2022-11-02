<?php

namespace App\Service;

use App\Entity\Entry;
use App\Entity\EntryImage;
use App\Repository\EntryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class EntryService
{
    /**
     * EntryService constructor.
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly LocationService $locationService, private readonly TagService $tagService, private readonly EntryRepository $entryRepository)
    {
    }

    /**
     * Save an entry.
     *
     * @param array     $entry Array of data for saving an entry object
     * @param File|null $image UploadFile object with containing image
     *
     * @return Entry $entryEntity        The saved entry entity
     */
    public function saveEntry(array $entry, ?File $image = null): Entry
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
