<?php

namespace App\Repository;

/**
 * SesionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SesionRepository extends \Doctrine\ORM\EntityRepository {

	public function findQbUltimaSesion() {
		$qb = $this->createQueryBuilder( 's' );

		$qb->orderBy( 's.id', 'DESC' )
		   ->andWhere( 's.activo = true' )
		   ->setMaxResults( 1 );

		return $qb;
	}

	public function findUltimaSesion() {
		$qb = $this->findQbUltimaSesion();

		return $qb->getQuery()->getScalarResult();
	}

	public function getQbAll() {
		$qb = $this->createQueryBuilder( 's' );

		$qb->orderBy( 's.fecha', 'DESC' );

		return $qb;
	}

	public function getQbBuscar( $filtros ) {
		$qb = $this->getQbAll();

		if ( $filtros ) {
			if ( isset( $filtros['titulo'] ) ) {
				$q = $filtros['titulo'];
				$qb->where( 'upper(s.titulo) LIKE upper(:titulo)' )
				   ->setParameter( 'titulo', "%$q%" );
			}
			if ( isset( $filtros['fecha'] ) ) {
				$qb->andWhere( 's.fecha = :fecha' )
				   ->setParameter( 'fecha', $filtros['fecha'] );
			}
			if ( isset( $filtros['tipo'] ) ) {
				$qb->andWhere( 's.tipoSesion = :tipo' )
				   ->setParameter( 'tipo', $filtros['tipo'] );
			}

		}

		return $qb;
	}


}