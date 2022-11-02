<?php

namespace App\Repository;

use App\Entity\Entry;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

/**
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Entry::class);
    }

    /**
     * Find entries query by a tag.
     */
    public function findEntriesByTagQuery(Tag $tag): Query
    {
        return $this->createQueryBuilder('e')
            ->where(':tag MEMBER OF e.tags')
            ->setParameters(['tag' => $tag])
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery();
    }

    public function findByTimestamp(Entry $entry, string $compare = '<', string $order = 'DESC'): ?Entry
    {
        $qb = $this->createQueryBuilder('e')
            ->where("e.timestamp {$compare} :timestamp")
            ->andWhere('e != :entry')
            ->orderBy('e.timestamp', $order)
            ->setMaxResults(1)
            ->setParameters([
                'entry' => $entry,
                'timestamp' => $entry->getTimestamp(),
            ])
            ->getQuery();

        return $qb->getOneOrNullResult();
    }

    /**
     * Return query to load all entries.
     */
    public function getFindAllQuery(): Query
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * Find an entry by criteria
     * Need this special function, because of translatable
     * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232.
     */
    public function findOneByCriteria(string $locale, array $params = []): ?Entry
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($params as $column => $value) {
            $qb->andWhere("e.$column = :$column")
                ->setParameter($column, $value);
        }

        $query = $qb->getQuery();

        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class)
            ->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        return $query->getOneOrNullResult();
    }
}
