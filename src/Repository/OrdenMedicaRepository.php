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

    public function findUltimas(){
        $previous_week = strtotime("-1 week +1 day");
        $start_week = strtotime("last sunday midnight",$previous_week);
        $end_week = strtotime("next saturday",$start_week);      
        $start_week = date("Y-m-d",$start_week);
        $end_week = date("Y-m-d",$end_week);

        return $this->createQueryBuilder('o')
            ->andWhere('o.hasta >= :comienzo and o.hasta <= :fin')
            ->setParameter('comienzo', $start_week)
            ->setParameter('fin', $end_week)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
