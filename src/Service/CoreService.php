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
     * Creates a new entity by its name.
     */
    public function createNewEntityByName(string $repoName, $choice): ?int
    {
        if (empty($choice = trim($choice))) {
            return null;
        }

        $repo = $this->em->getRepository($repoName);

        if (!$entity = $repo->find($choice)) {
            $class = $repo->getClassName();

            $entity = new $class();
            $entity->setName($choice);

            $this->em->persist($entity);
            $this->em->flush();
        }

        return $entity->getId();
    }
}
