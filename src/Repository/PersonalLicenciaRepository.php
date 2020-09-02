<?php

namespace App\Repository;

use App\Entity\PersonalLicencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalLicencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalLicencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalLicencia[]    findAll()
 * @method PersonalLicencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalLicenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalLicencia::class);
    }

    // /**
    //  * @return PersonalLicencia[] Returns an array of PersonalLicencia objects
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
    public function findOneBySomeField($value): ?PersonalLicencia
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

	public function getQbAll( $filter = null ) {
		$qb = $this->createQueryBuilder( 'pl' );

		if ( $filter['legajo'] ) {
			$qb->where( 'pl.legajo = :legajoId' )
			   ->setParameter( 'legajoId', $filter['legajo'] );
		}

		return $qb;
	}
}
