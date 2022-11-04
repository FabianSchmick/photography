<?php

namespace App\Tests\Repository;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Tests\Builder\EntryBuilder;
use App\Tests\Builder\TagBuilder;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private TagRepository $tagRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->tagRepository = $this->entityManager->getRepository(Tag::class);

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testFindRelatedTagsByTagReturnsMostRelatedTags(): void
    {
        $testTags = $this->createTestTags();
        $entryBuilder = new EntryBuilder($this->entityManager);
        $entryBuilder->addTag($testTags[0]);
        $entryBuilder->addTag($testTags[1]);
        $entryBuilder->create();
        $entryBuilder->addTag($testTags[1]);
        $entryBuilder->create();
        $entryBuilder->addTag($testTags[1]);
        $entryBuilder->create();

        $relatedTags = $this->tagRepository->findRelatedTagsByTag($testTags[0]);

        $this->assertCount(1, $relatedTags);
        $this->assertEquals($testTags[1], $relatedTags[0]);
    }

    /**
     * @return Tag[]
     */
    private function createTestTags(): array
    {
        $tags = [];
        $tagBuilder = new TagBuilder($this->entityManager);
        $tagBuilder->setName('Nature');
        $tagBuilder->setSort(100);
        $tags[] = $tagBuilder->create();
        $tagBuilder->setName('Snow');
        $tagBuilder->setSort(90);
        $tags[] = $tagBuilder->create();
        $tagBuilder->setName('Landscape');
        $tagBuilder->setSort(80);
        $tags[] = $tagBuilder->create();

        return $tags;
    }
}
