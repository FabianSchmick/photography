<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use HTMLPurifier;
use HTMLPurifier_Config;

class CoreService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * EntryService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string|null $string The string to purify
     *
     * @return string $string     The purified string
     */
    public function purifyString(?string $string): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', ['a', 'b', 'strong', 'ul', 'li', 'p', 'br']);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($string);
    }

    /**
     * Saves a new entity by its name.
     */
    public function saveNewEntityByName($choice, string $class, string $repo): int
    {
        $entity = $this->em->getRepository($repo)->find($choice);

        if (!$entity) {
            $entity = new $class();
            $entity->setName($choice);
            $this->em->persist($entity);
            $this->em->flush();
        }

        return $entity->getId();
    }
}
