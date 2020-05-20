<?php

namespace App\Repository;

use App\Entity\TourCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TourCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TourCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TourCategory[]    findAll()
 * @method TourCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TourCategory::class);
    }

    // /**
    //  * @return TourCategory[] Returns an array of TourCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TourCategory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
