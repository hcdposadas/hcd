<?php

namespace App\Repository;

use App\Entity\Proveido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proveido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proveido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proveido[]    findAll()
 * @method Proveido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProveidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proveido::class);
    }

    // /**
    //  * @return Proveido[] Returns an array of Proveido objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Proveido
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
