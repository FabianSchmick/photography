<?php

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Location;
use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
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
     * The quality for jpeg images
     *
     * @var int $imageQuality
     */
    private $imageQuality;

    /**
     * The height for thumbnails
     *
     * @var int $thumbHeight
     */
    private $thumbHeight;


    /**
     * EntryService constructor.
     *
     * @param   EntityManager     $em           Entity Manager
     * @param   string            $imageDir     Directory for uploaded images
     * @param   int               $imageQuality The quality for jpeg images
     * @param   int               $thumbHeight  The height for thumbnails
     */
    public function __construct(EntityManager $em, $imageDir, $imageQuality = 75, $thumbHeight = 500)
    {
        $this->em = $em;
        $this->imageDir = $imageDir;
        $this->imageQuality = $imageQuality;
        $this->thumbHeight = $thumbHeight;
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
            if (!file_exists($this->imageDir. '/thumb')) {    // Create image and thumbnail directory
                mkdir($this->imageDir . '/thumb', 0777, true);
            }

            // Random name and convert to jpg later
            $imageName = md5(uniqid()) . '.jpg';

            $imagine = new Imagine();
            $newImage = $imagine->open($image['image']->getPathName());
            $newImage->strip();
            $newImage->save($this->imageDir . '/' . $imageName, array('jpeg_quality' => $this->imageQuality));  // Save and minify image

            /** @var Box $size */
            $size = $newImage->getSize();

            $newImage
                ->thumbnail($size->heighten($this->thumbHeight))
                ->save($this->imageDir . '/thumb/' . $imageName, array('jpeg_quality' => $this->imageQuality))  // Save and minfiy thumbnail
            ;
        }
        $entryEntity->setTitle($entry['title']);
        $entryEntity->setDescription($entry['description']);

        if (!empty($entry['author'])) {
            $authorEntity = $this->em->getRepository('AppBundle:Author')->findOneBy(['name' => $entry['author']]);

            if (!$authorEntity) {
                $authorEntity = new Author();
            }
            $authorEntity->setName($entry['author']);
            $this->em->persist($authorEntity);

            $entryEntity->setAuthor($authorEntity);
        }

        $entryEntity->setImage($imageName);

        if (!empty($entry['location'])) {
            $locationEntity = $this->em->getRepository('AppBundle:Location')->findOneBy(['name' => $entry['location']]);

            if (!$locationEntity) {
                $locationEntity = new Location();
            }
            $locationEntity->setName($entry['location']);
            $this->em->persist($locationEntity);

            $entryEntity->setLocation($locationEntity);
        }

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
