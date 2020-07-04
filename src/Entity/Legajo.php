<?php

namespace App\Entity;

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
	 * @ORM\Column(name="situacion_revista", type="string", length=255)
	 */
	private $situacionRevista;

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
	 * @var string
	 *
	 * @ORM\Column(name="tratamiento", type="string", length=255, nullable=true)
	 */
	private $tratamiento;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="profesion", type="string", length=255, nullable=true)
	 */
	private $profesion;

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
    public function setFechaIngreso($fechaIngreso)
    {
        $this->fechaIngreso = $fechaIngreso;

        return $this;
    }

    /**
     * Get fechaIngreso
     *
     * @return \DateTime
     */
    public function getFechaIngreso()
    {
        return $this->fechaIngreso;
    }

    /**
     * Set tratamiento
     *
     * @param string $tratamiento
     *
     * @return Legajo
     */
    public function setTratamiento($tratamiento)
    {
        $this->tratamiento = $tratamiento;

        return $this;
    }

    /**
     * Get tratamiento
     *
     * @return string
     */
    public function getTratamiento()
    {
        return $this->tratamiento;
    }

    /**
     * Set profesion
     *
     * @param string $profesion
     *
     * @return Legajo
     */
    public function setProfesion($profesion)
    {
        $this->profesion = $profesion;

        return $this;
    }

    /**
     * Get profesion
     *
     * @return string
     */
    public function getProfesion()
    {
        return $this->profesion;
    }
}
