<?php

namespace App\Repository;

use App\Entity\AprobadoEnSesion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AprobadoEnSesion|null find($id, $lockMode = null, $lockVersion = null)
 * @method AprobadoEnSesion|null findOneBy(array $criteria, array $orderBy = null)
 * @method AprobadoEnSesion[]    findAll()
 * @method AprobadoEnSesion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AprobadoEnSesionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AprobadoEnSesion::class);
    }

    // /**
    //  * @return AprobadoEnSesion[] Returns an array of AprobadoEnSesion objects
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
    public function findOneBySomeField($value): ?AprobadoEnSesion
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
