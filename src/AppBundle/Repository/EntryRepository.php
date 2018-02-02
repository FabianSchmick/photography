<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Author;
use AppBundle\Entity\Location;
use AppBundle\Entity\Tag;
use Doctrine\ORM\Query;

/**
 * EntryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EntryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find entries query by a tag
     *
     * @param Tag $tag
     * @return Query
     */
    public function findEntriesByTag(Tag $tag)
    {
        $qb = $this->createQueryBuilder("e")
            ->where(':tag MEMBER OF e.tags')
            ->setParameters(array('tag' => $tag))
            ->orderBy('e.timestamp', 'DESC');
        return $qb->getQuery();
    }

    /**
     * Find entries by an author
     *
     * @param Author $author
     * @return array
     */
    public function findEntriesByAuthor(Author $author)
    {
        $qb = $this->createQueryBuilder("e");
        $qb ->where($qb->expr()->eq('e.author', $author->getId()));
        return $qb->getQuery()->getResult();
    }

    /**
     * Find entries by a location
     *
     * @param Location $location
     * @return array
     */
    public function findEntriesByLocation(Location $location)
    {
        $qb = $this->createQueryBuilder("e");
        $qb ->where($qb->expr()->eq('e.location', $location->getId()));
        return $qb->getQuery()->getResult();
    }

    /**
     * Return query to load all entries
     *
     * @return \Doctrine\ORM\Query  The query
     */
    public function getFindAllQuery()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityName(), 'e')
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery();
        return $query;
    }

    /**
     * Find an entry by criteria
     * Need this special function, because of translatable
     * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232
     *
     * @param $params
     * @return mixed
     */
    public function findOneByCriteria(array $params)
    {
        $query = $this->createQueryBuilder('e');

        $i = 0;
        foreach ($params as $column => $value) {
            if ($i < 1) {
                $query->where("e.$column = :$column");
            } else {
                $query->andWhere("e.$column = :$column");
            }
            $query->setParameter($column, $value);

            $i++;
        }

        $query = $query->getQuery();


        $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

        return $query->getOneOrNullResult();
    }
}
