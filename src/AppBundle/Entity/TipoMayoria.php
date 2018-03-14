<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * TipoMocion
 *
 * @ORM\Table(name="tipo_mayoria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TipoMayoriaRepository")
 */
class TipoMayoria extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $nombre
	 *
	 * @ORM\Column(name="nombre", type="string", length=255)
	 */
	private $nombre;

	/**
	 * @var string $funcion
	 *
	 * @ORM\Column(name="funcion", type="string", length=255)
	 */
	private $funcion;

	public function __toString() {
		return $this->getNombre();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 *
	 * @return TipoMayoria
	 */
	public function setNombre( $nombre ) {
		$this->nombre = $nombre;

		return $this;
	}

	/**
	 * Get nombre
	 *
	 * @return string
	 */
	public function getNombre() {
		return $this->nombre;
	}

	/**
	 * @return string
	 */
	public function getFuncion() {
		return $this->funcion;
	}

	/**
	 * @param string $funcion
	 */
	public function setFuncion( $funcion ) {
		$this->funcion = $funcion;
	}

	/**
	 * @return array
	 */
	public static function funciones() {
		return array(
			'mayoriaSimple', // mitad mas uno
			'mayoriaCalificada', // 2/3 del cuerpo
			'mayoriaCalificadaPresentes' // 10
		);
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return bool
	 */
	public function mayoriaSimple( Mocion $mocion ) {
//		return ( $mocion->getCuentaAfirmativos() > ( intval( $mocion->getCuentaTotal() / 2 ) / 2 + 1 ) );
		$return = false;

		$afirmativos = $mocion->getCuentaAfirmativos();
		$negativos = $mocion->getCuentaNegativos();

		if ($mocion->getCuentaTotal() >= 8 && $afirmativos == $negativos) {
		    // El cargo mayor debe estar más arriba
		    // ID_cargo => valor_del_voto (inicialmente null)
		    $ordenCargos = array(
		        23 => null, // presidente
                20 => null, // vicepresidente 1
                21 => null, // vicepresidente 2
            );

		    foreach ($mocion->getVotos() as $voto) {
		        if ($voto->esAbstencion()) {
		            continue;
                }

		        $cargoPersona = $voto->getConcejal()->getPersona()->getCargoPersona();
		        foreach ($cargoPersona as $cp) {
		            if (in_array($cp->getCargo()->getId(), array_keys($ordenCargos))) {
		                $ordenCargos[$cp->getCargo()->getId()] = $voto;
                    }
                }
            }

            foreach ($ordenCargos as $idCargo => $voto) {
		        if ($voto != null) {
		            return $voto->esAfirmativo();
                }
            }
        }

		switch ( $mocion->getCuentaTotal() ) {
			case 14:
				if ( $mocion->getCuentaAfirmativos() >= 8 ) {
					$return = true;
				}
				break;
			case 13:
				if ( $mocion->getCuentaAfirmativos() >= 7 ) {
					$return = true;
				}
				break;
			case 12:
				if ( $mocion->getCuentaAfirmativos() >= 7 ) {
					$return = true;
				}
				break;
			case 11:
				if ( $mocion->getCuentaAfirmativos() >= 6 ) {
					$return = true;
				}
				break;
			case 10:
				if ( $mocion->getCuentaAfirmativos() >= 6 ) {
					$return = true;
				}
				break;
			case 9:
				if ( $mocion->getCuentaAfirmativos() >= 5 ) {
					$return = true;
				}
				break;
			case 8:
				if ( $mocion->getCuentaAfirmativos() >= 5 ) {
					$return = true;
				}
				break;
			default:
				$return = false;

		}

		return $return;
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return bool
	 */
	public function mayoriaCalificada( Mocion $mocion ) {
		return ( $mocion->getCuentaAfirmativos() >= 10 );
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return bool
	 */
	public function mayoriaCalificadaPresentes( Mocion $mocion ) {


		$return = false;
		switch ( $mocion->getCuentaTotal() ) {
			case 14:
				if ( $mocion->getCuentaAfirmativos() >= 10 ) {
					$return = true;
				}
				break;
			case 13:
				if ( $mocion->getCuentaAfirmativos() >= 9 ) {
					$return = true;
				}
				break;
			case 12:
				if ( $mocion->getCuentaAfirmativos() >= 8 ) {
					$return = true;
				}
				break;
			case 11:
				if ( $mocion->getCuentaAfirmativos() >= 8 ) {
					$return = true;
				}
				break;
			case 10:
				if ( $mocion->getCuentaAfirmativos() >= 7 ) {
					$return = true;
				}
				break;
			case 9:
				if ( $mocion->getCuentaAfirmativos() >= 6 ) {
					$return = true;
				}
				break;
			case 8:
				if ( $mocion->getCuentaAfirmativos() >= 6 ) {
					$return = true;
				}
				break;
			default:
				$return = false;

		}

		return $return;
	}
}

