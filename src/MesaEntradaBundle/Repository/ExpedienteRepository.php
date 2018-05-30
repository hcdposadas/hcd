<?php

namespace MesaEntradaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MesaEntradaBundle\Entity\Dictamen;
use MesaEntradaBundle\Entity\Expediente;

/**
 * ExpedienteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExpedienteRepository extends EntityRepository {
	public function search( $data ) {
		$qb = $this->createQueryBuilder( 'e' );

		$qb->where( 'e.expediente = :expediente' );

		$qb->setParameter( 'expediente', $data['expediente'] );

// ['tipoExpediente']
// ['expediente']
// ['anio']
// ['letra']
// ['fecha']
// ['registroMunicipal']
// ['areaAdministrativa']
// ['iniciador'] =>
// ['iniciadorParticular']

		return $qb->getQuery()->getResult();

	}

	public function getQbAll() {
		$qb = $this->createQueryBuilder( 'e' );

		$qb->orderBy( 'e.id', 'DESC' );

		return $qb;
	}

	public function getQbExpedientesMesaEntrada() {
		$qb = $this->getQbAll();
		$qb->where( 'e.borrador is null' )
		   ->orWhere( 'e.borrador = false' );

		return $qb;
	}

	public function getQbExpedientesMesaEntradaTipo( $tipoExpediente ) {
		$qb = $this->getQbAll();
		$qb
			->orWhere( 'e.borrador = false' )
			->andWhere( 'e.tipoExpediente = :tipoExpediente' )
			->setParameter( 'tipoExpediente', $tipoExpediente );

		return $qb;
	}

	public function getQbExpedientesLegislativoTipo( $tipoExpediente ) {
		$qb = $this->getQbAll();
		$qb
			->andWhere( 'e.tipoExpediente = :tipoExpediente' )
			->setParameter( 'tipoExpediente', $tipoExpediente );

		return $qb;
	}

	public function getQbBuscar( $data, $tipoExpediente = null ) {
		$qb = $this->getQbExpedientesMesaEntrada();

		if ( isset( $data['tipoExpediente'] ) ) {
			$qb->andWhere( 'e.tipoExpediente = :tipoExpediente' )
			   ->setParameter( 'tipoExpediente', $data['tipoExpediente'] );
		}
		if ( $tipoExpediente ) {
			$qb->andWhere( 'e.tipoExpediente = :tipoExpediente' )
			   ->setParameter( 'tipoExpediente', $tipoExpediente );
		}
		if ( $data['textoDefinitivo'] ) {
			$q = $data['textoDefinitivo'];
			$qb->andWhere( "UPPER(e.textoDefinitivo) LIKE UPPER(:textoDefinitivo)" )
			   ->setParameter( 'textoDefinitivo', "%$q%" );
		}
		if ( $data['extracto'] ) {
			$q = $data['extracto'];
			//			todo ver acentos
			$qb->andWhere( 'UPPER(e.extracto) LIKE UPPER(:extracto)' )
			   ->setParameter( 'extracto', "%$q%" );
		}
		if ( $data['expediente'] ) {
			$q = $data['expediente'];
			$qb->andWhere( 'UPPER(e.expediente) LIKE UPPER(:expediente)' )
			   ->setParameter( 'expediente', "%$q%" );
		}
		if ( $data['letra'] ) {
			$q = $data['letra'];
			$qb->andWhere( 'UPPER(e.letra) LIKE UPPER(:letra)' )
			   ->setParameter( 'letra', "%$q%" );
		}
		if ( $data['texto'] ) {
			$q = $data['texto'];
			$qb->andWhere( 'UPPER(e.texto) LIKE UPPER(:texto)' )
			   ->setParameter( 'texto', "%$q%" );
		}
		if ( $data['registroMunicipal'] ) {
			$q = $data['registroMunicipal'];
			$qb->andWhere( 'UPPER(e.registroMunicipal) LIKE UPPER(:registroMunicipal)' )
			   ->setParameter( 'registroMunicipal', "%$q%" );
		}
		if ( $data['iniciador'] ) {
//			$qb->andWhere( 'e.iniciador = :iniciador' )
//			   ->setParameter( 'iniciador', $data['iniciador'] );
			$q = $data['iniciador'];
			$qb->join( 'e.iniciadores', 'iniciadores' )
			   ->join( 'iniciadores.iniciador', 'iniciador' )
			   ->join( 'iniciador.cargoPersona', 'cargoPersona' )
			   ->join( 'cargoPersona.persona', 'persona' )
			   ->andWhere( 'upper(persona.nombre) LIKE upper(:iniciador)' )
			   ->orWhere( 'upper(persona.apellido) LIKE upper(:iniciador)' )
			   ->setParameter( 'iniciador', "%$q%" );
		}
		if ( $data['iniciadorParticular'] ) {

			$q = $data['iniciadorParticular'];
			$qb->join( 'e.iniciadorParticular', 'iniciadorParticular' )
			   ->andWhere( 'upper(iniciadorParticular.nombre) LIKE upper(:iniciadorParticular)' )
			   ->setParameter( 'iniciadorParticular', "%$q%" );
		}
		if ( $data['dependencia'] ) {
			$q = $data['dependencia'];
			$qb->join( 'e.dependencia', 'dependencia' )
			   ->andWhere( 'upper(dependencia.nombre) LIKE upper(:dependencia)' )
			   ->setParameter( 'dependencia', "%$q%" );
		}

		if ( ( $data['fecha'] ) ) {
			$qb->andWhere( 'e.fecha = :fecha' );
			$qb->setParameter( 'fecha', $data['fecha'] );
		}
		if ( ( $data['anio'] ) ) {
			$qb->andWhere( 'e.anio = :anio' );
			$qb->setParameter( 'anio', $data['anio'] );
		}

		return $qb;

	}

	public function getQbProyecetosPorConcejal( $concejal, $data = null ) {

		$qb = $this->getQbAll();


		$qb->join( 'e.iniciadores', 'iniciadores' )
		   ->where( 'iniciadores.iniciador = :concejal' );

		$qb->setParameter( 'concejal', $concejal );

		$qb->innerJoin( 'e.tipoExpediente', 'te' )
		   ->andWhere( 'te.slug = :teSlug' )
		   ->setParameter( 'teSlug', 'externo' );

		if ( isset( $data['expediente'] ) ) {
			$q = $data['expediente'];
			$qb->andWhere( 'e.expediente = :expediente' )
			   ->setParameter( 'expediente', $q );
		}

		if ( isset( $data['anio'] ) ) {
			$qb->andWhere( 'e.anio = :anio' );
			$qb->setParameter( 'anio', $data['anio'] );
		}

		if ( isset( $data['letra'] ) ) {
			$qb->andWhere( 'UPPER(e.letra) = UPPER(:letra)' );
			$qb->setParameter( 'letra', $data['letra'] );
		}

		if ( isset( $data['texto'] ) ) {
			$q = $data['texto'];
			$qb->andWhere( 'UPPER(e.texto) LIKE UPPER(:texto)' )
			   ->setParameter( 'texto', "%$q%" );
		}

		if ( isset( $data['fecha'] ) ) {
			$qb->andWhere( 'e.fecha = :fecha' );
			$qb->setParameter( 'fecha', $data['fecha'] );
		}

		if ( isset( $data['tipoProyecto'] ) ) {
			$q = $data['tipoProyecto'];
			$qb->andWhere( 'e.tipoProyecto = :tipoProyecto' )
			   ->setParameter( 'tipoProyecto', $q );
		}


		return $qb;

	}

	public function getQbExpedientes( $data = null ) {
		$qb = $this->createQueryBuilder( 'e' );

		$qb->innerJoin( 'e.tipoExpediente', 'te' )
		   ->where( 'te.slug = :teSlug' )
		   ->setParameter( 'teSlug', 'externo' );

		if ( $data['expediente'] ) {
			$q = $data['expediente'];
			$qb->andWhere( 'e.expediente = :expediente' )
			   ->setParameter( 'expediente', $q );
		}

		if ( isset( $data['anio'] ) ) {
			$qb->andWhere( 'e.anio = :anio' );
			$qb->setParameter( 'anio', $data['anio'] );
		}

		if ( isset( $data['letra'] ) ) {
			$qb->andWhere( 'UPPER(e.letra) = UPPER(:letra)' );
			$qb->setParameter( 'letra', $data['letra'] );
		}

		if ( isset( $data['texto'] ) ) {
			$q = $data['texto'];
			$qb->andWhere( 'UPPER(e.texto) LIKE UPPER(:texto)' )
			   ->setParameter( 'texto', "%$q%" );
		}

		if ( isset( $data['tema'] ) ) {
			$q = $data['tema'];
			$qb->andWhere( 'UPPER(e.extracto) LIKE UPPER(:extracto)' )
			   ->setParameter( 'extracto', "%$q%" );
		}

		$qb->andWhere( 'e.borrador = false' );

		return $qb;
	}

	public function buscarExpedientesSesion( $data ) {
		$qb = $this->getQbExpedientes( $data );
		return $qb->getQuery()->getResult();
	}

	public function getQbExpedientesLegislativosExternos() {
		$qb = $this->getQbAll();

//		$qb->join('e.iniciadores', 'iniciadores')
//		   ->where('iniciadores is null');
		$qb->leftJoin( 'e.dependencia', 'dependencia' )
		   ->andWhere( 'dependencia.id is not null' );

		return $qb;
	}

	public function getQbBuscarExpedientesLegislativosExternos() {
		$qb = $this->getQbAll();

//		$qb->join('e.iniciadores', 'iniciadores')
//		   ->where('iniciadores is null');


		return $qb;
	}

	public function getProyectosBAE( $data ) {
		$qb = $this->getQbExpedientes( $data );

		$qb->join( 'e.periodoLegislativo', 'pl' );
		$qb->addSelect( 'pl' );

		return $qb->getQuery()->getArrayResult();
	}

    /**
     * @param $data
     * @return Dictamen[]
     */
	public function getDictamenesOD( $data )
    {
		$qb = $this->getQbExpedientes( $data );

		$qb->join( 'e.periodoLegislativo', 'pl' );
		$qb->addSelect( 'pl' );

        /** @var Expediente[] $expedientes */
        $expedientes = $qb->getQuery()->getResult();

        $dictamenes = [];
        foreach ($expedientes as $expediente) {
            /** @var Dictamen $dictamen */
            foreach ($expediente->getDictamenes() as $dictamen) {
                $dictamenes[] = $dictamen;
            }
        }

		return $dictamenes;
	}
}
