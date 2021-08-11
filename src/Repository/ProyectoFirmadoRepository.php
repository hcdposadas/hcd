<?php

namespace App\Repository;

use App\Entity\ProyectoFirmado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProyectoFirmado|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProyectoFirmado|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProyectoFirmado[]    findAll()
 * @method ProyectoFirmado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProyectoFirmadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProyectoFirmado::class);
    }

    // /**
    //  * @return ProyectoFirmado[] Returns an array of ProyectoFirmado objects
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
    public function findOneBySomeField($value): ?ProyectoFirmado
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
