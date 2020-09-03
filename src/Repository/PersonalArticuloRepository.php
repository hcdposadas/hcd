<?php

namespace App\Repository;

use App\Entity\PersonalArticulo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalArticulo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalArticulo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalArticulo[]    findAll()
 * @method PersonalArticulo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalArticuloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalArticulo::class);
    }

    // /**
    //  * @return PersonalArticulo[] Returns an array of PersonalArticulo objects
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
    public function findOneBySomeField($value): ?PersonalArticulo
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
