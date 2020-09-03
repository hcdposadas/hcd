<?php

namespace App\Repository;

use App\Entity\PersonalConyuge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalConyuge|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalConyuge|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalConyuge[]    findAll()
 * @method PersonalConyuge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalConyugeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalConyuge::class);
    }

    // /**
    //  * @return PersonalConyuge[] Returns an array of PersonalConyuge objects
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
    public function findOneBySomeField($value): ?PersonalConyuge
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
