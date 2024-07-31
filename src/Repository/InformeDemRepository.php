<?php

namespace App\Repository;

use App\Entity\InformeDem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InformeDem|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformeDem|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformeDem[]    findAll()
 * @method InformeDem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformeDemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformeDem::class);
    }

    // /**
    //  * @return InformeDem[] Returns an array of InformeDem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformeDem
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
