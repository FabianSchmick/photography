<?php

namespace AppBundle\Service;

use AppBundle\Entity\Entry;
use AppBundle\Entity\Tag;
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
     * Directory for uploaded images
     *
     * @var string $imageDir
     */
    private $imageDir;


    /**
     * EntryService constructor.
     *
     * @param   EntityManager     $em           Entity Manager
     * @param   string            $imageDir     Directory for uploaded images
     */
    public function __construct(EntityManager $em, $imageDir)
    {
        $this->em = $em;
        $this->imageDir = $imageDir;
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

        if (isset($image['image'])) {
            /** @var UploadedFile $image */
            $imageName = md5(uniqid()) . '.' . $image['image']->guessExtension();

            $image['image']->move(
                $this->imageDir,
                $imageName
            );
        }
        $entryEntity->setTitle($entry['title']);
        $entryEntity->setDescription($entry['description']);
        $entryEntity->setAuthor($entry['author']);
        $entryEntity->setImage($imageName);
        $entryEntity->setLocation($entry['location']);
        if ($timestamp = date_create(date($entry['timestamp']))) {
            $entryEntity->setTimestamp($timestamp);
        } else {
            $entryEntity->setTimestamp(new \DateTime("now"));
        }

        $tagsArrayCollection = new ArrayCollection();
        foreach ($entry['tags'] as $tag) {
            $tagEntity = $this->em->getRepository('AppBundle:Tag')->findOneBy(['name' => $tag]);

            if (!$tagEntity) {
                $tagEntity = new Tag();
            }
            $tagEntity->setName($tag);
            $this->em->persist($tagEntity);

            $tagsArrayCollection->add($tagEntity);
        }
        $entryEntity->setTags($tagsArrayCollection);

        $this->em->persist($entryEntity);
        $this->em->flush();

        return $entryEntity;
    }
}
