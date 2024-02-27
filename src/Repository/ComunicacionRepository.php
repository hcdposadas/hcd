<?php

namespace App\Repository;

use App\Entity\Comunicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comunicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comunicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comunicacion[]    findAll()
 * @method Comunicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComunicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comunicacion::class);
    }

    public function countComunicacionesByTipo($tipo , $areaOrigen){

        return $this->createQueryBuilder('c')
                    ->select('COUNT(c.id) as cantidad')
                    ->andWhere('c.tipo = :tipo')
                    ->andWhere('c.areaOrigen = :areaOrigen')
                    ->setParameter('tipo', $tipo)
                    ->setParameter('areaOrigen', $areaOrigen)
                    ->getQuery()->getSingleScalarResult();
    }


    // /**
    //  * @return Comunicacion[] Returns an array of Comunicacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comunicacion
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
