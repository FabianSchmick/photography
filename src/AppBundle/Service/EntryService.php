<?php

namespace AppBundle\Service;

use AppBundle\Entity\Entry;
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
     * @param   EntityManager     $em               Entity Manager
     * @param   AuthorService     $authorService    Author service
     * @param   LocationService   $locationService  Location service
     * @param   TagService        $tagService       Tag service
     * @param   string            $imageDir         Directory for uploaded images
     * @param   int               $imageQuality     The quality for jpeg images
     * @param   int               $thumbHeight      The height for thumbnails
     */
    public function __construct(EntityManager $em, AuthorService $authorService, LocationService $locationService, TagService $tagService, $imageDir, $imageQuality = 75, $thumbHeight = 500)
    {
        $this->em = $em;
        $this->authorService = $authorService;
        $this->locationService = $locationService;
        $this->tagService = $tagService;
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
            $authorEntity = $this->authorService->saveAuthor(['name' => $entry['author']]);

            $this->em->persist($authorEntity);

            $entryEntity->setAuthor($authorEntity);
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
