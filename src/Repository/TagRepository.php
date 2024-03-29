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
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Tag::class);
    }

    /**
     * Find related tags by a tag.
     * Method returns the tags from all posts which relate to the current tag under the following conditions:
     *      - excluding the requested tag
     *      - occurrence of min $count times
     *      - max $limit related tags.
     */
    public function findRelatedTagsByTag(Tag $tag, int $count = 3, int $limit = 10): array
    {
        $in = $this->getEntityManager()->getRepository(Post::class)
            ->createQueryBuilder('a_e')
            ->where(':tag MEMBER OF a_e.tags');

        $qb = $this->createQueryBuilder('b_t');
        $qb->innerJoin('b_t.posts', 'b_te')
            ->where($qb->expr()->in('b_te', $in->getDQL()))
            ->andWhere('b_t != :tag')
            ->orderBy('COUNT(b_t)', 'DESC')
            ->addOrderBy('b_t.sort', 'DESC')
            ->groupBy('b_t')
            ->having('COUNT(b_t) >= '.$count)
            ->setMaxResults($limit)
            ->setParameters(['tag' => $tag]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find a tag by criteria
     * Need this special function, because of translatable
     * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232.
     */
    public function findOneByCriteria(string $locale, array $params = []): ?Tag
    {
        $qb = $this->createQueryBuilder('t');

        foreach ($params as $column => $value) {
            $qb->andWhere("t.$column = :$column")
                ->setParameter($column, $value);
        }

        $query = $qb->getQuery();

        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class)
            ->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        return $query->getOneOrNullResult();
    }
}
