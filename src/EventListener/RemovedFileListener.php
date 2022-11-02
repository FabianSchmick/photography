<?php

namespace App\EventListener;

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
     * RemovedFileListener constructor.
     *
     * @param CacheManager   $cacheManager Liip Cache Manger for removing files from media/cache dir
     * @param UploaderHelper $helper       Vich UploadHelper to get the path
     */
    public function __construct(private readonly CacheManager $cacheManager, private readonly UploaderHelper $helper)
    {
    }

    /**
     * Remove files from media/cache dir, too.
     */
    public function onPreRemove(Event $event): void
    {
        if (!($event->getObject()->getFile() instanceof File)) {
            $removedFile = $event->getObject();
            $path = $this->helper->asset($removedFile, 'file');
            $this->cacheManager->remove($path);
        }
    }
}
