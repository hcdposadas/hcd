<?php

namespace App\Repository;

/**
 * ProyectoBAERepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProyectoBAERepository extends \Doctrine\ORM\EntityRepository {

	public function findByExpedienteTimeline( $expediente ) {

		$qb = $this->createQueryBuilder( 'pb' );

		$qb->where( 'pb.expediente = :expediente' )
		   ->setParameter( 'expediente', $expediente );

		$qb->leftJoin( 'pb.giros', 'giros' );

		$qb->addOrderBy( 'giros.orden', 'DESC' );
		$qb->addOrderBy( 'giros.fechaGiro', 'ASC' );

		return $qb->getQuery()->getResult();

	}

}
