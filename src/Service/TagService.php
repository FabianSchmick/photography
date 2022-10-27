<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class TagService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * TagService constructor.
     */
    public function __construct(EntityManagerInterface $em, TagRepository $tagRepository, string $defaultLocale)
    {
        $this->em = $em;
        $this->defaultLocale = $defaultLocale;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Save a tag.
     *
     * @param array $tag Array of data for saving a tag object
     *
     * @return Tag $tagEntity The saved tag entity
     */
    public function saveTag(array $tag): Tag
    {
        $tagEntity = new Tag();
        if (isset($tag['id'])) {
            $tagEntity = $this->tagRepository->findOneBy(['id' => $tag['id']]);
        } else {
            $duplicate = $this->tagRepository->findOneByCriteria($this->defaultLocale, ['name' => $tag['name']]);
        }

        if (!empty($duplicate)) {
            return $duplicate;
        }

        $tagEntity->setName($tag['name']);

        if (!empty($tag['description'])) {
            $tagEntity->setDescription($tag['description']);
        }

        $this->em->persist($tagEntity);
        $this->em->flush();

        return $tagEntity;
    }
}
