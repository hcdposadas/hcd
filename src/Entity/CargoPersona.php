<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * CargoPersona
 *
 * @ORM\Table(name="cargo_persona")
 * @ORM\Entity(repositoryClass="App\Repository\CargoPersonaRepository")
 */
class CargoPersona extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Persona", inversedBy="cargoPersona", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Cargo")
	 * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
	 */
	private $cargo;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_administrativa_id", referencedColumnName="id")
	 */
	private $areaAdministrativa;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Comision")
	 * @ORM\JoinColumn(name="comision_id", referencedColumnName="id", nullable=true)
	 */
	private $comision;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\Iniciador", mappedBy="cargoPersona", cascade={"persist", "remove"})
	 */
	private $iniciador;

	public function __toString() {
		return $this->getCargo()->getNombre() .' '.  $this->getPersona();
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
	 * Set persona
	 *
	 * @param \App\Entity\Persona $persona
	 *
	 * @return CargoPersona
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
	 * Set cargo
	 *
	 * @param \App\Entity\Cargo $cargo
	 *
	 * @return CargoPersona
	 */
	public function setCargo( \App\Entity\Cargo $cargo = null ) {
		$this->cargo = $cargo;

		return $this;
	}

	/**
	 * Get cargo
	 *
	 * @return \App\Entity\Cargo
	 */
	public function getCargo() {
		return $this->cargo;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return CargoPersona
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
	 * @return CargoPersona
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
	 * @return CargoPersona
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
	 * @return CargoPersona
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

    /**
     * Set areaAdministrativa
     *
     * @param \App\Entity\AreaAdministrativa $areaAdministrativa
     *
     * @return CargoPersona
     */
    public function setAreaAdministrativa(\App\Entity\AreaAdministrativa $areaAdministrativa = null)
    {
        $this->areaAdministrativa = $areaAdministrativa;

        return $this;
    }

    /**
     * Get areaAdministrativa
     *
     * @return \App\Entity\AreaAdministrativa
     */
    public function getAreaAdministrativa()
    {
        return $this->areaAdministrativa;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->iniciador = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add iniciador
     *
     * @param \App\Entity\Iniciador $iniciador
     *
     * @return CargoPersona
     */
    public function addIniciador(\App\Entity\Iniciador $iniciador)
    {
        $this->iniciador[] = $iniciador;

        return $this;
    }

    /**
     * Remove iniciador
     *
     * @param \App\Entity\Iniciador $iniciador
     */
    public function removeIniciador(\App\Entity\Iniciador $iniciador)
    {
        $this->iniciador->removeElement($iniciador);
    }

    /**
     * Get iniciador
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIniciador()
    {
        return $this->iniciador;
    }

    /**
     * Set comision
     *
     * @param \App\Entity\Comision $comision
     *
     * @return CargoPersona
     */
    public function setComision(\App\Entity\Comision $comision = null)
    {
        $this->comision = $comision;

        return $this;
    }

    /**
     * Get comision
     *
     * @return \App\Entity\Comision
     */
    public function getComision()
    {
        return $this->comision;
    }
}
