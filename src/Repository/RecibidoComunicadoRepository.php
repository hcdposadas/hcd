<?php

namespace App\Repository;

use App\Entity\RecibidoComunicado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecibidoComunicado|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecibidoComunicado|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecibidoComunicado[]    findAll()
 * @method RecibidoComunicado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecibidoComunicadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecibidoComunicado::class);
    }

    // /**
    //  * @return RecibidoComunicado[] Returns an array of RecibidoComunicado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecibidoComunicado
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
