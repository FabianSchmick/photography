<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Post::class);
    }

    /**
     * Find posts query by a tag.
     */
    public function findPostsByTagQuery(Tag $tag): Query
    {
        return $this->createQueryBuilder('e')
            ->where(':tag MEMBER OF e.tags')
            ->setParameters(['tag' => $tag])
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery();
    }

    public function findByTimestamp(Post $post, string $compare = '<', string $order = 'DESC'): ?Post
    {
        $qb = $this->createQueryBuilder('e')
            ->where("e.timestamp {$compare} :timestamp")
            ->andWhere('e != :post')
            ->orderBy('e.timestamp', $order)
            ->setMaxResults(1)
            ->setParameters([
                'post' => $post,
                'timestamp' => $post->getTimestamp(),
            ])
            ->getQuery();

        return $qb->getOneOrNullResult();
    }

    /**
     * Return query to load all posts.
     */
    public function getFindAllQuery(): Query
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * Find an post by criteria
     * Need this special function, because of translatable
     * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232.
     */
    public function findOneByCriteria(string $locale, array $params = []): ?Post
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
