<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;


/**
 * Persona
 *
 * @ORM\Table(name="persona")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonaRepository")
 */
class Persona extends BaseClass {
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
	 * @var string
	 *
	 * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
	 */
	private $telefono;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="celular", type="string", length=255, nullable=true)
	 */
	private $celular;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="mail", type="string", length=255, nullable=true)
	 */
	private $mail;


	/**
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\DomicilioPersona", mappedBy="persona", cascade={"persist", "remove"})
	 */
	private $domicilioPersona;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\CargoPersona", mappedBy="persona", cascade={"persist", "remove"})
	 */
	private $cargoPersona;

	public function __toString() {
		return $this->nombre . ' ' . $this->apellido;
	}

	public function getNombreCompleto() {

		return $this->nombre . ' ' . $this->apellido;

	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->domicilioPersona = new \Doctrine\Common\Collections\ArrayCollection();
		$this->cargoPersona     = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set telefono
	 *
	 * @param string $telefono
	 *
	 * @return Persona
	 */
	public function setTelefono( $telefono ) {
		$this->telefono = $telefono;

		return $this;
	}

	/**
	 * Get telefono
	 *
	 * @return string
	 */
	public function getTelefono() {
		return $this->telefono;
	}

	/**
	 * Set celular
	 *
	 * @param string $celular
	 *
	 * @return Persona
	 */
	public function setCelular( $celular ) {
		$this->celular = $celular;

		return $this;
	}

	/**
	 * Get celular
	 *
	 * @return string
	 */
	public function getCelular() {
		return $this->celular;
	}

	/**
	 * Set mail
	 *
	 * @param string $mail
	 *
	 * @return Persona
	 */
	public function setMail( $mail ) {
		$this->mail = $mail;

		return $this;
	}

	/**
	 * Get mail
	 *
	 * @return string
	 */
	public function getMail() {
		return $this->mail;
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
	 * Add domicilioPersona
	 *
	 * @param \AppBundle\Entity\DomicilioPersona $domicilioPersona
	 *
	 * @return Persona
	 */
	public function addDomicilioPersona( \AppBundle\Entity\DomicilioPersona $domicilioPersona ) {
		$domicilioPersona->setPersona( $this );
		$this->domicilioPersona->add( $domicilioPersona );

		return $this;
	}

	public function addDomicilioPersonon( \AppBundle\Entity\DomicilioPersona $domicilioPersona ) {
		$domicilioPersona->setPersona( $this );
		$this->domicilioPersona->add( $domicilioPersona );

		return $this;
	}

	/**
	 * Remove domicilioPersona
	 *
	 * @param \AppBundle\Entity\DomicilioPersona $domicilioPersona
	 */
	public function removeDomicilioPersona( \AppBundle\Entity\DomicilioPersona $domicilioPersona ) {
		$this->domicilioPersona->removeElement( $domicilioPersona );
	}

	public function removeDomicilioPersonon( \AppBundle\Entity\DomicilioPersona $domicilioPersona ) {
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
	 * Add cargoPersona
	 *
	 * @param \AppBundle\Entity\CargoPersona $cargoPersona
	 *
	 * @return Persona
	 */
	public function addCargoPersona( \AppBundle\Entity\CargoPersona $cargoPersona ) {
		$cargoPersona->setPersona( $this );
		$this->cargoPersona->add( $cargoPersona );

		return $this;
	}

	/**
	 * Remove cargoPersona
	 *
	 * @param \AppBundle\Entity\CargoPersona $cargoPersona
	 */
	public function removeCargoPersona( \AppBundle\Entity\CargoPersona $cargoPersona ) {
		$this->cargoPersona->removeElement( $cargoPersona );
	}

	/**
	 * Add cargoPersona
	 *
	 * @param \AppBundle\Entity\CargoPersona $cargoPersona
	 *
	 * @return Persona
	 */
	public function addCargoPersonon( \AppBundle\Entity\CargoPersona $cargoPersona ) {
		$cargoPersona->setPersona( $this );
		$this->cargoPersona->add( $cargoPersona );

		return $this;
	}

	/**
	 * Remove cargoPersona
	 *
	 * @param \AppBundle\Entity\CargoPersona $cargoPersona
	 */
	public function removeCargoPersonon( \AppBundle\Entity\CargoPersona $cargoPersona ) {
		$this->cargoPersona->removeElement( $cargoPersona );
	}

	/**
	 * Get cargoPersona
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCargoPersona() {
		return $this->cargoPersona;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return Persona
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
	 * @return Persona
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}
}
