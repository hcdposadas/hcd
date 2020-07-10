<?php

namespace App\Repository;

use App\Entity\PersonalDeclaracionJurada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalDeclaracionJurada|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalDeclaracionJurada|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalDeclaracionJurada[]    findAll()
 * @method PersonalDeclaracionJurada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalDeclaracionJuradaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalDeclaracionJurada::class);
    }

    // /**
    //  * @return PersonalDeclaracionJurada[] Returns an array of PersonalDeclaracionJurada objects
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
    public function findOneBySomeField($value): ?PersonalDeclaracionJurada
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
