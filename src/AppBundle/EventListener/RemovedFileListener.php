<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * https://github.com/dustin10/VichUploaderBundle/issues/523#issuecomment-254858575.
 */
class RemovedFileListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $helper;

    /**
     * RemovedFileListener constructor.
     *
     * @param EntityManagerInterface $em             Entity Manager
     * @param CacheManager           $cacheManager   Liip Cache Manger for removing files from media/cache dir
     * @param UploaderHelper         $uploaderHelper Liip UploadHelper to get the path
     */
    public function __construct(EntityManagerInterface $em, CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->em = $em;
        $this->cacheManager = $cacheManager;
        $this->helper = $uploaderHelper;
    }

    /**
     * Remove files from media/cache dir, too.
     */
    public function onPreRemove(Event $event)
    {
        if (!($event->getObject()->getFile() instanceof File)) {
            $removedFile = $event->getObject();
            $path = $this->helper->asset($removedFile, 'file');
            $this->cacheManager->remove($path);
        }
    }

    /**
     * Make sure a file entity object is removed after the file is deleted.
     */
    public function onPostRemove(Event $event)
    {
        if (!($event->getObject()->getFile() instanceof File)) {
            $removedFile = $event->getObject();
            $this->em->remove($removedFile);
            $this->em->flush();
        }
    }
}
