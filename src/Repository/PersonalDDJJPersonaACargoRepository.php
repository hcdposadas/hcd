<?php

namespace App\Repository;

use App\Entity\PersonalDDJJPersonaACargo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalDDJJPersonaACargo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalDDJJPersonaACargo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalDDJJPersonaACargo[]    findAll()
 * @method PersonalDDJJPersonaACargo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalDDJJPersonaACargoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalDDJJPersonaACargo::class);
    }

    // /**
    //  * @return PersonalDDJJPersonaACargo[] Returns an array of PersonalDDJJPersonaACargo objects
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
    public function findOneBySomeField($value): ?PersonalDDJJPersonaACargo
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
