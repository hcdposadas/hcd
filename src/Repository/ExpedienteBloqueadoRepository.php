<?php

namespace App\Repository;

use App\Entity\ExpedienteBloqueado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpedienteBloqueado|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpedienteBloqueado|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpedienteBloqueado[]    findAll()
 * @method ExpedienteBloqueado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpedienteBloqueadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpedienteBloqueado::class);
    }

    // /**
    //  * @return ExpedienteBloqueado[] Returns an array of ExpedienteBloqueado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpedienteBloqueado
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
