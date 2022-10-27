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
}
