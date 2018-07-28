<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Event\Event;

/**
 * https://github.com/dustin10/VichUploaderBundle/issues/523#issuecomment-254858575
 */
class RemovedFileListener
{
    /**
     * Entity Manager
     *
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * RemovedFileListener constructor.
     *
     * @param   EntityManagerInterface  $em  Entity Manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Make sure a file entity object is removed after the file is deleted
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
