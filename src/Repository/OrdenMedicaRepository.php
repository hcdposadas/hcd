<?php

namespace App\Repository;

use App\Entity\OrdenMedica;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdenMedica|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdenMedica|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdenMedica[]    findAll()
 * @method OrdenMedica[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenMedicaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdenMedica::class);
    }

    // /**
    //  * @return OrdenMedica[] Returns an array of OrdenMedica objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdenMedica
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
