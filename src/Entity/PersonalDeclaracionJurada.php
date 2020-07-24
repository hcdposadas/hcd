<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalDeclaracionJuradaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PersonalDeclaracionJuradaRepository::class)
 * @UniqueEntity(
 *     fields={"legajo", "anio"},
 *     errorPath="anio",
 *     message="Ya Existe una DDJJ para este año"
 * )
 */
class PersonalDeclaracionJurada extends BaseClass {

	const NIVEL_ESTUDIOS_PRIMARIO_COMPLETO = 'Primario Completo';
	const NIVEL_ESTUDIOS_PRIMARIO_INCOMPLETO = 'Primario Incompleto';
	const NIVEL_ESTUDIOS_SECUNDARIO_COMPLETO = 'Secundario Completo';
	const NIVEL_ESTUDIOS_SECUNDARIO_INCOMPLETO = 'Secundario Incompleto';
	const NIVEL_ESTUDIOS_TERCIARIO_COMPLETO = 'Terciario Completo';
	const NIVEL_ESTUDIOS_TERCIARIO_INCOMPLETO = 'Terciario Incompleto';
	const NIVEL_ESTUDIOS_UNIVERSITARIO_COMPLETO = 'Universitario Completo';
	const NIVEL_ESTUDIOS_UNIVERSITARIO_INCOMPLETO = 'Universitario Incompleto';

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=Legajo::class, inversedBy="personalDeclaracionJuradas")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $legajo;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $anio;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tratamiento", type="string", length=255, nullable=true)
	 */
	private $tratamiento;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="situacion_revista", type="string", length=255)
	 */
	private $situacionRevista;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="profesion", type="string", length=255, nullable=true)
	 */
	private $profesion;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $nivelEstudios;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $titulo;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $aniosCursados;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $fechaPresentacion;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $estadoCivil;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $categoria;

	/**
	 * @ORM\ManyToOne(targetEntity=PersonalLugarTrabajo::class, cascade={"persist"})
	 */
	private $lugarTrabajo;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\PersonalDDJJPersonaACargo", mappedBy="ddjj", cascade={"persist"})
	 */
	private $personalDDJJPersonaACargos;

	/**
	 * @ORM\OneToMany(targetEntity=PersonalDDJJConyuge::class, mappedBy="ddjj", cascade={"persist"})
	 */
	private $personalDDJJConyuges;

//	public function __construct() {
//		$this->personalDDJJPersonaACargos = new ArrayCollection();
//		$this->personalDDJJConyuges       = new ArrayCollection();
//	}


	public function getId(): ?int {
		return $this->id;
	}

	public function getLegajo(): ?Legajo {
		return $this->legajo;
	}

	public function setLegajo( ?Legajo $legajo ): self {
		$this->legajo = $legajo;

		return $this;
	}

	public function getAnio(): ?int {
		return $this->anio;
	}

	public function setAnio( int $anio ): self {
		$this->anio = $anio;

		return $this;
	}

	public function getFechaPresentacion(): ?\DateTimeInterface {
		return $this->fechaPresentacion;
	}

	public function setFechaPresentacion( ?\DateTimeInterface $fechaPresentacion ): self {
		$this->fechaPresentacion = $fechaPresentacion;

		return $this;
	}

	/**
	 * Set tratamiento
	 *
	 * @param string $tratamiento
	 *
	 * @return Legajo
	 */
	public function setTratamiento( $tratamiento ) {
		$this->tratamiento = $tratamiento;

		return $this;
	}

	/**
	 * Get tratamiento
	 *
	 * @return string
	 */
	public function getTratamiento() {
		return $this->tratamiento;
	}

	/**
	 * Set profesion
	 *
	 * @param string $profesion
	 *
	 * @return Legajo
	 */
	public function setProfesion( $profesion ) {
		$this->profesion = $profesion;

		return $this;
	}

	/**
	 * Get profesion
	 *
	 * @return string
	 */
	public function getProfesion() {
		return $this->profesion;
	}

	public function getNivelEstudios(): ?string {
		return $this->nivelEstudios;
	}

	public function setNivelEstudios( ?string $nivelEstudios ): self {
		$this->nivelEstudios = $nivelEstudios;

		return $this;
	}

	public function getTitulo(): ?string {
		return $this->titulo;
	}

	public function setTitulo( ?string $titulo ): self {
		$this->titulo = $titulo;

		return $this;
	}

	public function getAniosCursados(): ?int {
		return $this->aniosCursados;
	}

	public function setAniosCursados( ?int $aniosCursados ): self {
		$this->aniosCursados = $aniosCursados;

		return $this;
	}

	/**
	 * Set situacionRevista
	 *
	 * @param string $situacionRevista
	 *
	 * @return Legajo
	 */
	public function setSituacionRevista( $situacionRevista ) {
		$this->situacionRevista = $situacionRevista;

		return $this;
	}

	/**
	 * Get situacionRevista
	 *
	 * @return string
	 */
	public function getSituacionRevista() {
		return $this->situacionRevista;
	}


	public function getEstadoCivil(): ?string {
		return $this->estadoCivil;
	}

	public function setEstadoCivil( ?string $estadoCivil ): self {
		$this->estadoCivil = $estadoCivil;

		return $this;
	}

	public function getCategoria(): ?string {
		return $this->categoria;
	}

	public function setCategoria( ?string $categoria ): self {
		$this->categoria = $categoria;

		return $this;
	}

	public function getLugarTrabajo(): ?PersonalLugarTrabajo {
		return $this->lugarTrabajo;
	}

	public function setLugarTrabajo( ?PersonalLugarTrabajo $lugarTrabajo ): self {
		$this->lugarTrabajo = $lugarTrabajo;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getPersonalDDJJPersonaACargos(): ?Collection {
		return $this->personalDDJJPersonaACargos;
	}


	/**
	 * @param mixed $personalDDJJPersonaACargos
	 */
	public function setPersonalDDJJPersonaACargos( $personalDDJJPersonaACargos ) {

		foreach ( $personalDDJJPersonaACargos as $item ) {

			$this->personalDDJJPersonaACargos->add( $item );
			$item->setDdjj( $this );
		}

		return $this;
	}

	/**
	 * Add addPersonalDDJJPersonaACargos
	 *
	 * @param \App\Entity\PersonalDDJJPersonaACargo $personalDDJJPersonaACargos
	 *
	 * @return PersonalDeclaracionJurada
	 */
	public function addPersonalDDJJPersonaACargo( PersonalDDJJPersonaACargo $personalDDJJPersonaACargos ) {

		$personalDDJJPersonaACargos->setDdjj( $this );

		$this->personalDDJJPersonaACargos->add( $personalDDJJPersonaACargos );

//		return $this;
	}


	/**
	 * Remove personalDDJJPersonaACargos
	 *
	 * @param \App\Entity\PersonalDDJJPersonaACargo $personalDDJJPersonaACargos
	 */
	public function removePersonalDDJJPersonaACargo( \App\Entity\PersonalDDJJPersonaACargo $personalDDJJPersonaACargos
	) {
		$this->personalDDJJPersonaACargos->removeElement( $personalDDJJPersonaACargos );
	}


	/**
	 * @return Collection
	 */
	public function getPersonalDDJJConyuges(): ?Collection {
		return $this->personalDDJJConyuges;
	}


	/**
	 * Add personalDDJJConyuges
	 *
	 * @param \App\Entity\PersonalDDJJConyuge $personalDDJJConyuges
	 *
	 * @return PersonalDeclaracionJurada
	 */
	public function addPersonalDDJJConyuge( \App\Entity\PersonalDDJJConyuge $personalDDJJConyuges ) {

		$personalDDJJConyuges->setDdjj( $this );

		$this->personalDDJJConyuges->add( $personalDDJJConyuges );

		return $this;
	}


	/**
	 * Remove personalDDJJConyuges
	 *
	 * @param \App\Entity\PersonalDDJJConyuge $personalDDJJConyuges
	 */
	public function removePersonalDDJJConyuge( \App\Entity\PersonalDDJJConyuge $personalDDJJConyuges
	) {
		$this->personalDDJJConyuges->removeElement( $personalDDJJConyuges );
	}

}
