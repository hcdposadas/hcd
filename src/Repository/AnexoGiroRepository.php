<?php

namespace App\Repository;

use App\Entity\AnexoGiro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnexoGiro|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnexoGiro|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnexoGiro[]    findAll()
 * @method AnexoGiro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnexoGiroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnexoGiro::class);
    }

    // /**
    //  * @return AnexoGiro[] Returns an array of AnexoGiro objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnexoGiro
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
