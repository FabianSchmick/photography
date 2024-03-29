<?php

namespace App\Repository;

use App\Entity\Tour;
use App\Entity\TourCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

/**
 * @method Tour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tour[]    findAll()
 * @method Tour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Tour::class);
    }

    /**
     * Return query to load all or filtered posts.
     */
    public function getFindAllQuery(?TourCategory $category = null): Query
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.sort', 'DESC')
            ->addOrderBy('t.updated', 'DESC');

        if ($category) {
            $qb = $qb->where('t.tourCategory = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery();
    }

    /**
     * Get the page for $tour on the tour index page.
     *
     * @return int The page where $tour is find
     */
    public function findTourListPageNumber(Tour $tour, ?TourCategory $category = null): int
    {
        $tours = $this->getFindAllQuery($category)->getResult();

        $pos = array_search($tour, $tours);

        return (int) ceil(($pos + 1) / Tour::PAGINATION_QUANTITY);
    }

    /**
     * Find a tag by criteria
     * Need this special function, because of translatable
     * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232.
     */
    public function findOneByCriteria(string $locale, array $params = []): ?Tour
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
