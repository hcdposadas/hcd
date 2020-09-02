<?php

namespace App\Repository;

use App\Entity\PersonalAsistencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalAsistencia|null find( $id, $lockMode = null, $lockVersion = null )
 * @method PersonalAsistencia|null findOneBy( array $criteria, array $orderBy = null )
 * @method PersonalAsistencia[]    findAll()
 * @method PersonalAsistencia[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class PersonalAsistenciaRepository extends ServiceEntityRepository {
	public function __construct( ManagerRegistry $registry ) {
		parent::__construct( $registry, PersonalAsistencia::class );
	}

	// /**
	//  * @return PersonalAsistencia[] Returns an array of PersonalAsistencia objects
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
	public function findOneBySomeField($value): ?PersonalAsistencia
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
		$qb = $this->createQueryBuilder( 'pa' );

		if ( $filter['legajo'] ) {
			$qb->where( 'pa.legajo = :legajoId' )
			   ->setParameter( 'legajoId', $filter['legajo'] );
		}

		return $qb;
	}


}
