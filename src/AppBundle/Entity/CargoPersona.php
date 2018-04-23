<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * CargoPersona
 *
 * @ORM\Table(name="cargo_persona")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CargoPersonaRepository")
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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona", inversedBy="cargoPersona", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cargo")
	 * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
	 */
	private $cargo;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_administrativa_id", referencedColumnName="id")
	 */
	private $areaAdministrativa;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comision")
	 * @ORM\JoinColumn(name="comision_id", referencedColumnName="id", nullable=true)
	 */
	private $comision;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="MesaEntradaBundle\Entity\Iniciador", mappedBy="cargoPersona", cascade={"persist", "remove"})
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
	 * @param \AppBundle\Entity\Persona $persona
	 *
	 * @return CargoPersona
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

	/**
	 * Set cargo
	 *
	 * @param \AppBundle\Entity\Cargo $cargo
	 *
	 * @return CargoPersona
	 */
	public function setCargo( \AppBundle\Entity\Cargo $cargo = null ) {
		$this->cargo = $cargo;

		return $this;
	}

	/**
	 * Get cargo
	 *
	 * @return \AppBundle\Entity\Cargo
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
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return CargoPersona
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
	 * @return CargoPersona
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

    /**
     * Set areaAdministrativa
     *
     * @param \AppBundle\Entity\AreaAdministrativa $areaAdministrativa
     *
     * @return CargoPersona
     */
    public function setAreaAdministrativa(\AppBundle\Entity\AreaAdministrativa $areaAdministrativa = null)
    {
        $this->areaAdministrativa = $areaAdministrativa;

        return $this;
    }

    /**
     * Get areaAdministrativa
     *
     * @return \AppBundle\Entity\AreaAdministrativa
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
     * @param \MesaEntradaBundle\Entity\Iniciador $iniciador
     *
     * @return CargoPersona
     */
    public function addIniciador(\MesaEntradaBundle\Entity\Iniciador $iniciador)
    {
        $this->iniciador[] = $iniciador;

        return $this;
    }

    /**
     * Remove iniciador
     *
     * @param \MesaEntradaBundle\Entity\Iniciador $iniciador
     */
    public function removeIniciador(\MesaEntradaBundle\Entity\Iniciador $iniciador)
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
     * @param \AppBundle\Entity\Comision $comision
     *
     * @return CargoPersona
     */
    public function setComision(\AppBundle\Entity\Comision $comision = null)
    {
        $this->comision = $comision;

        return $this;
    }

    /**
     * Get comision
     *
     * @return \AppBundle\Entity\Comision
     */
    public function getComision()
    {
        return $this->comision;
    }
}
