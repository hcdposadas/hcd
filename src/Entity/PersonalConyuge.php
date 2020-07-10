<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalConyugeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalConyugeRepository::class)
 */
class PersonalConyuge extends BaseClass {

	const ESTADO_CIVIL_SOLTERX ='Soltera/a';
	const ESTADO_CIVIL_CASADX ='Casada/a';
	const ESTADO_CIVIL_DIVORCIADX ='Divorciada/o';
	const ESTADO_CIVIL_SEPARADX ='Separada/o';
	const ESTADO_CIVIL_VIUDX ='Viuda/o';
	const ESTADO_CIVIL_CONVIVIENTE ='Conviviente';

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $estado;

	/**
	 * @ORM\ManyToOne(targetEntity=Persona::class)
	 */
	private $persona;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $fechaEnlace;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $lugarEnlace;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $trabaja;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $razonSocialLugarTrabajo;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $percibeAsignacionFamiliar;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $observaciones;

	public function getId(): ?int {
		return $this->id;
	}

	public function getEstado(): ?string {
		return $this->estado;
	}

	public function setEstado( string $estado ): self {
		$this->estado = $estado;

		return $this;
	}

	public function getPersona(): ?Persona {
		return $this->persona;
	}

	public function setPersona( ?Persona $persona ): self {
		$this->persona = $persona;

		return $this;
	}

	public function getFechaEnlace(): ?\DateTimeInterface {
		return $this->fechaEnlace;
	}

	public function setFechaEnlace( ?\DateTimeInterface $fechaEnlace ): self {
		$this->fechaEnlace = $fechaEnlace;

		return $this;
	}

	public function getLugarEnlace(): ?string {
		return $this->lugarEnlace;
	}

	public function setLugarEnlace( ?string $lugarEnlace ): self {
		$this->lugarEnlace = $lugarEnlace;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTrabaja() {
		return $this->trabaja;
	}

	/**
	 * @param mixed $trabaja
	 */
	public function setTrabaja( $trabaja ): void {
		$this->trabaja = $trabaja;
	}

	public function getRazonSocialLugarTrabajo(): ?string {
		return $this->razonSocialLugarTrabajo;
	}

	public function setRazonSocialLugarTrabajo( ?string $razonSocialLugarTrabajo ): self {
		$this->razonSocialLugarTrabajo = $razonSocialLugarTrabajo;

		return $this;
	}

	public function getPercibeAsignacionFamiliar(): ?bool {
		return $this->percibeAsignacionFamiliar;
	}

	public function setPercibeAsignacionFamiliar( bool $percibeAsignacionFamiliar ): self {
		$this->percibeAsignacionFamiliar = $percibeAsignacionFamiliar;

		return $this;
	}

	public function getObservaciones(): ?string {
		return $this->observaciones;
	}

	public function setObservaciones( ?string $observaciones ): self {
		$this->observaciones = $observaciones;

		return $this;
	}
}
