<?php

namespace App\Service;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

class AuthorService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * AuthorService constructor.
     */
    public function __construct(EntityManagerInterface $em, AuthorRepository $authorRepository)
    {
        $this->em = $em;
        $this->authorRepository = $authorRepository;
    }

    /**
     * Save an author.
     *
     * @param array $author Array of data for saving an author object
     *
     * @return Author $authorEntity     The saved author entity
     */
    public function saveAuthor(array $author): Author
    {
        $duplicate = $this->authorRepository->findOneBy(['name' => $author['name']]);

        if ($duplicate) {
            return $duplicate;
        }
        $authorEntity = new Author();
        if (isset($author['id'])) {
            $authorEntity = $this->authorRepository->findOneBy(['id' => $author['id']]);
        }
        $authorEntity->setName($author['name']);

        $this->em->persist($authorEntity);
        $this->em->flush();

        return $authorEntity;
    }
}
