<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Iniciador
 *
 * @ORM\Table(name="iniciador")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\IniciadorRepository")
 */
class Iniciador extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona")
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	public function __toString() {
		$personaCargo = $this->persona->getCargoPersona()->first() . ' ' . $this->persona->__toString();

		return $personaCargo;

	}

	public function getIniciadorString() {
		$personaCargo = $this->persona->getCargoPersona()->first() . ' ' . $this->persona->__toString();

		return $personaCargo;
	}


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return Iniciador
	 */
	public function setFechaCreacion( $fechaCreacion ) {
		$this->fechaCreacion = $fechaCreacion;

		return $this;
	}

	/**
	 * Set fechaActualizacion
	 *
	 * @param \DateTime $fechaActualizacion
	 *
	 * @return Iniciador
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return Iniciador
	 */
	public function setCreadoPor( \UsuariosBundle\Entity\Usuario $creadoPor = null ) {
		$this->creadoPor = $creadoPor;

		return $this;
	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $actualizadoPor
	 *
	 * @return Iniciador
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Set persona
	 *
	 * @param \AppBundle\Entity\Persona $persona
	 *
	 * @return Iniciador
	 */
	public function setPersona( \AppBundle\Entity\Persona $persona = null ) {
		$this->persona = $persona;

		return $this;
	}

	/**
	 * Get persona
	 *
	 * @return \AppBundle\Entity\Persona
	 */
	public function getPersona() {
		return $this->persona;
	}
}
