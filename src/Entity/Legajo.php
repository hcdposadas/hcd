<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * Legajo
 *
 * @ORM\Table(name="personal_legajo")
 * @ORM\Entity(repositoryClass="App\Repository\LegajoRepository")
 */
class Legajo extends BaseClass {
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
	 * @ORM\Column(name="numero", type="string", length=255, nullable=true, unique=true)
	 */
	private $numero;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_tarjeta", type="string", length=255, nullable=true, unique=true)
	 */
	private $numeroTarjeta;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="observaciones", type="text", nullable=true)
	 */
	private $observaciones;

	/**
	 * @var
	 *
	 * @ORM\OneToOne(targetEntity="App\Entity\Persona", inversedBy="legajo", cascade={"persist"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\Column(name="fecha_ingreso", type="date",nullable=true)
	 */
	private $fechaIngreso;

	/**
	 * @ORM\OneToMany(targetEntity=PersonalNovedad::class, mappedBy="legajo", orphanRemoval=true)
	 */
	private $personalNovedads;

	/**
	 * @ORM\OneToMany(targetEntity=PersonalAsistencia::class, mappedBy="legajo", orphanRemoval=true)
	 */
	private $personalAsistencias;

	/**
	 * @ORM\OneToMany(targetEntity=PersonalDeclaracionJurada::class, mappedBy="legajo", orphanRemoval=true)
	 */
	private $personalDeclaracionJuradas;

	public function __construct() {
		$this->personalNovedads           = new ArrayCollection();
		$this->personalAsistencias        = new ArrayCollection();
		$this->personalDeclaracionJuradas = new ArrayCollection();
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
	 * Set numero
	 *
	 * @param string $numero
	 *
	 * @return Legajo
	 */
	public function setNumero( $numero ) {
		$this->numero = $numero;

		return $this;
	}

	/**
	 * Get numero
	 *
	 * @return string
	 */
	public function getNumero() {
		return $this->numero;
	}

	/**
	 * Set numeroTarjeta
	 *
	 * @param string $numeroTarjeta
	 *
	 * @return Legajo
	 */
	public function setNumeroTarjeta( $numeroTarjeta ) {
		$this->numeroTarjeta = $numeroTarjeta;

		return $this;
	}

	/**
	 * Get numeroTarjeta
	 *
	 * @return string
	 */
	public function getNumeroTarjeta() {
		return $this->numeroTarjeta;
	}


	/**
	 * Set observaciones
	 *
	 * @param string $observaciones
	 *
	 * @return Legajo
	 */
	public function setObservaciones( $observaciones ) {
		$this->observaciones = $observaciones;

		return $this;
	}

	/**
	 * Get observaciones
	 *
	 * @return string
	 */
	public function getObservaciones() {
		return $this->observaciones;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return Legajo
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
	 * @return Legajo
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return Legajo
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
	 * @return Legajo
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Set persona
	 *
	 * @param \App\Entity\Persona $persona
	 *
	 * @return Legajo
	 */
	public function setPersona( \App\Entity\Persona $persona = null ) {
		$this->persona = $persona;

		return $this;
	}

	/**
	 * Get persona
	 *
	 * @return \App\Entity\Persona
	 */
	public function getPersona() {
		return $this->persona;
	}

	/**
	 * Set fechaIngreso
	 *
	 * @param \DateTime $fechaIngreso
	 *
	 * @return Legajo
	 */
	public function setFechaIngreso( $fechaIngreso ) {
		$this->fechaIngreso = $fechaIngreso;

		return $this;
	}

	/**
	 * Get fechaIngreso
	 *
	 * @return \DateTime
	 */
	public function getFechaIngreso() {
		return $this->fechaIngreso;
	}

	/**
	 * @return Collection|PersonalNovedad[]
	 */
	public function getPersonalNovedads(): Collection {
		return $this->personalNovedads;
	}

	public function addPersonalNovedad( PersonalNovedad $personalNovedad ): self {
		if ( ! $this->personalNovedads->contains( $personalNovedad ) ) {
			$this->personalNovedads[] = $personalNovedad;
			$personalNovedad->setLegajo( $this );
		}

		return $this;
	}

	public function removePersonalNovedad( PersonalNovedad $personalNovedad ): self {
		if ( $this->personalNovedads->contains( $personalNovedad ) ) {
			$this->personalNovedads->removeElement( $personalNovedad );
			// set the owning side to null (unless already changed)
			if ( $personalNovedad->getLegajo() === $this ) {
				$personalNovedad->setLegajo( null );
			}
		}

		return $this;
	}

	/**
	 * @return Collection|PersonalAsistencia[]
	 */
	public function getPersonalAsistencias(): Collection {
		return $this->personalAsistencias;
	}

	public function addPersonalAsistencia( PersonalAsistencia $personalAsistencia ): self {
		if ( ! $this->personalAsistencias->contains( $personalAsistencia ) ) {
			$this->personalAsistencias[] = $personalAsistencia;
			$personalAsistencia->setLegajo( $this );
		}

		return $this;
	}

	public function removePersonalAsistencia( PersonalAsistencia $personalAsistencia ): self {
		if ( $this->personalAsistencias->contains( $personalAsistencia ) ) {
			$this->personalAsistencias->removeElement( $personalAsistencia );
			// set the owning side to null (unless already changed)
			if ( $personalAsistencia->getLegajo() === $this ) {
				$personalAsistencia->setLegajo( null );
			}
		}

		return $this;
	}

	/**
	 * @return Collection|PersonalDeclaracionJurada[]
	 */
	public function getPersonalDeclaracionJuradas(): Collection {
		return $this->personalDeclaracionJuradas;
	}

	public function addPersonalDeclaracionJurada( PersonalDeclaracionJurada $personalDeclaracionJurada ): self {
		if ( ! $this->personalDeclaracionJuradas->contains( $personalDeclaracionJurada ) ) {
			$this->personalDeclaracionJuradas[] = $personalDeclaracionJurada;
			$personalDeclaracionJurada->setLegajo( $this );
		}

		return $this;
	}

	public function removePersonalDeclaracionJurada( PersonalDeclaracionJurada $personalDeclaracionJurada ): self {
		if ( $this->personalDeclaracionJuradas->contains( $personalDeclaracionJurada ) ) {
			$this->personalDeclaracionJuradas->removeElement( $personalDeclaracionJurada );
			// set the owning side to null (unless already changed)
			if ( $personalDeclaracionJurada->getLegajo() === $this ) {
				$personalDeclaracionJurada->setLegajo( null );
			}
		}

		return $this;
	}
}
