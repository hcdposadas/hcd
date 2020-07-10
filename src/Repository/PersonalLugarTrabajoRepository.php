<?php

namespace App\Repository;

use App\Entity\PersonalLugarTrabajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalLugarTrabajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalLugarTrabajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalLugarTrabajo[]    findAll()
 * @method PersonalLugarTrabajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalLugarTrabajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalLugarTrabajo::class);
    }

    // /**
    //  * @return PersonalLugarTrabajo[] Returns an array of PersonalLugarTrabajo objects
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
    public function findOneBySomeField($value): ?PersonalLugarTrabajo
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
