<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;


/**
 * Persona
 *
 * @ORM\Table(name="persona")
 * @ORM\Entity(repositoryClass="App\Repository\PersonaRepository")
 */
class Persona extends BaseClass {

	const PERSONA_GENERO_FEMENINO = 'Femenino';
	const PERSONA_GENERO_MASCULINO = 'Masculino';
	const PERSONA_GENERO_NE = 'Prefiero no especificarlo';

	const PERSONA_TIPO_DOCUMENTO_DNI = 'DNI';
	const PERSONA_TIPO_DOCUMENTO_CI = 'CI';
	const PERSONA_TIPO_DOCUMENTO_LE = 'LE';
	const PERSONA_TIPO_DOCUMENTO_LC = 'LC';

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255)
	 */
	private $nombre;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="apellido", type="string", length=255)
	 */
	private $apellido;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="dni", type="string", length=255, nullable=true)
	 */
	private $dni;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_nacimiento", type="date", nullable=true)
	 */
	private $fechaNacimiento;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\DomicilioPersona", mappedBy="persona", cascade={"persist", "remove"})
	 */
	protected $domicilioPersona;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\CargoPersona", mappedBy="persona", cascade={"persist", "remove"})
	 */
	private $cargoPersona;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\ContactoPersona", mappedBy="persona", cascade={"persist", "remove"})
	 */
	private $contactoPersona;

	/**
	 *
	 * @ORM\OneToOne(targetEntity="App\Entity\Legajo", mappedBy="persona", cascade={"persist", "remove"})
	 */
	private $legajo;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $lugarNacimiento;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $genero;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tipoDocumento;

	public function __toString() {
		return $this->nombre . ' ' . $this->apellido;
	}

	public function getNombreCompleto() {

		return $this->apellido . ' ' . $this->nombre;

	}

	public function esPresidenteComision() {
		foreach ( $this->cargoPersona as $cargoPersona ) {
			if ( strtoupper( $cargoPersona->getCargo()->getNombre() ) == 'PRESIDENTE' &&
			     $cargoPersona->getComision()
			) {
				return $cargoPersona;
			}
		}

		return false;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->domicilioPersona = new \Doctrine\Common\Collections\ArrayCollection();
		$this->cargoPersona     = new \Doctrine\Common\Collections\ArrayCollection();
		$this->contactoPersona  = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set nombre
	 *
	 * @param string $nombre
	 *
	 * @return Persona
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
	 * Set apellido
	 *
	 * @param string $apellido
	 *
	 * @return Persona
	 */
	public function setApellido( $apellido ) {
		$this->apellido = $apellido;

		return $this;
	}

	/**
	 * Get apellido
	 *
	 * @return string
	 */
	public function getApellido() {
		return $this->apellido;
	}

	/**
	 * Set dni
	 *
	 * @param string $dni
	 *
	 * @return Persona
	 */
	public function setDni( $dni ) {
		$this->dni = $dni;

		return $this;
	}

	/**
	 * Get dni
	 *
	 * @return string
	 */
	public function getDni() {
		return $this->dni;
	}

	/**
	 * Set fechaNacimiento
	 *
	 * @param \DateTime $fechaNacimiento
	 *
	 * @return Persona
	 */
	public function setFechaNacimiento( $fechaNacimiento ) {
		$this->fechaNacimiento = $fechaNacimiento;

		return $this;
	}

	/**
	 * Get fechaNacimiento
	 *
	 * @return \DateTime
	 */
	public function getFechaNacimiento() {
		return $this->fechaNacimiento;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return Persona
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
	 * @return Persona
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * @param mixed $domicilioPersona
	 */
	public function setDomicilioPersona( $domicilioPersona ) {

		foreach ( $domicilioPersona as $item ) {

			$this->domicilioPersona->add( $item );
			$item->setPersona( $this );
		}

		return $this;
	}

	/**
	 * Add domicilioPersona
	 *
	 * @param \App\Entity\DomicilioPersona $domicilioPersona
	 *
	 * @return Persona
	 */
	public function addDomicilioPersona( \App\Entity\DomicilioPersona $domicilioPersona ) {

		$domicilioPersona->setPersona( $this );

		$this->domicilioPersona->add( $domicilioPersona );

		return $this;
	}

	/**
	 * Remove domicilioPersona
	 *
	 * @param \App\Entity\DomicilioPersona $domicilioPersona
	 */
	public function removeDomicilioPersona( \App\Entity\DomicilioPersona $domicilioPersona ) {
		$this->domicilioPersona->removeElement( $domicilioPersona );
	}

	/**
	 * Get domicilioPersona
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getDomicilioPersona() {
		return $this->domicilioPersona;
	}

	/**
	 * @param mixed $cargoPersona
	 */
	public function setCargoPersona( $cargoPersona ) {

		foreach ( $cargoPersona as $item ) {

			$this->cargoPersona->add( $item );
			$item->setPersona( $this );
		}

		return $this;
	}

	/**
	 * Add cargoPersona
	 *
	 * @param \App\Entity\CargoPersona $cargoPersona
	 *
	 * @return Persona
	 */
	public function addCargoPersona( \App\Entity\CargoPersona $cargoPersona ) {

		$cargoPersona->setPersona( $this );

		$this->cargoPersona->add( $cargoPersona );

		return $this;
	}

	/**
	 * Remove cargoPersona
	 *
	 * @param \App\Entity\CargoPersona $cargoPersona
	 */
	public function removeCargoPersona( \App\Entity\CargoPersona $cargoPersona ) {
		$this->cargoPersona->removeElement( $cargoPersona );
	}

	/**
	 * Get cargoPersona
	 *
	 * @return CargoPersona[]|\Doctrine\Common\Collections\Collection
	 */
	public function getCargoPersona() {
		return $this->cargoPersona;
	}


	/**
	 * @param mixed $domicilioPersona
	 */
	public function setContactoPersona( $contactoPersona ) {

		foreach ( $contactoPersona as $item ) {

			$this->contactoPersona->add( $item );
			$item->setPersona( $this );
		}

		return $this;
	}

	/**
	 * Add contactoPersona
	 *
	 * @param \App\Entity\ContactoPersona $contactoPersona
	 *
	 * @return Persona
	 */
	public function addContactoPersona( \App\Entity\ContactoPersona $contactoPersona ) {
		
		$contactoPersona->setPersona( $this );

		$this->contactoPersona->add( $contactoPersona );

		return $this;
	}

	/**
	 * Remove contactoPersona
	 *
	 * @param \App\Entity\ContactoPersona $contactoPersona
	 */
	public function removeContactoPersona( \App\Entity\ContactoPersona $contactoPersona ) {
		$this->contactoPersona->removeElement( $contactoPersona );
	}

	/**
	 * Get contactoPersona
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getContactoPersona() {
		return $this->contactoPersona;
	}

	/**
	 * Set legajo
	 *
	 * @param \App\Entity\Legajo $legajo
	 *
	 * @return Persona
	 */
	public function setLegajo( \App\Entity\Legajo $legajo = null ) {
		$this->legajo = $legajo;

		$legajo->setPersona( $this );

		return $this;
	}

	/**
	 * Get legajo
	 *
	 * @return \App\Entity\Legajo
	 */
	public function getLegajo() {
		return $this->legajo;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return Persona
	 */
	public function setCreadoPor( \App\Entity\Usuario $creadoPor = null ) {
		$this->creadoPor = $creadoPor;

		return $this;
	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \App\Entity\Usuario $actualizadoPor
	 *
	 * @return Persona
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	public function getLugarNacimiento(): ?string {
		return $this->lugarNacimiento;
	}

	public function setLugarNacimiento( ?string $lugarNacimiento ): self {
		$this->lugarNacimiento = $lugarNacimiento;

		return $this;
	}


	public function getTipoDocumento(): ?string {
		return $this->tipoDocumento;
	}

	public function setTipoDocumento( ?string $tipoDocumento ): self {
		$this->tipoDocumento = $tipoDocumento;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGenero() {
		return $this->genero;
	}

	/**
	 * @param mixed $genero
	 */
	public function setGenero( $genero ): void {
		$this->genero = $genero;
	}


}
