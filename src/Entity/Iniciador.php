<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * Iniciador
 *
 * @ORM\Table(name="iniciador")
 * @ORM\Entity(repositoryClass="App\Repository\IniciadorRepository")
 */
class Iniciador extends BaseClass
{
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
     * @ORM\ManyToOne(targetEntity="App\Entity\CargoPersona", inversedBy="iniciador")
     * @ORM\JoinColumn(name="cargo_persona_id", referencedColumnName="id")
     */
    private $cargoPersona;

    public function __toString() : string
    {
        $cargoPersonaCargo = $this->cargoPersona->getCargo() . ' ' . $this->cargoPersona->getPersona()->getNombreCompleto();

        return $cargoPersonaCargo;

    }

//	public function getIniciadorString() {
//		$cargoPersonaCargo = $this->cargoPersona . ' ' . $this->cargoPersona->__toString();
//
//		return $cargoPersonaCargo;
//	}


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Iniciador
     */
    public function setFechaCreacion($fechaCreacion)
    {
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
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set cargoPersona
     *
     * @param \App\Entity\CargoPersona $cargoPersona
     *
     * @return Iniciador
     */
    public function setCargoPersona(\App\Entity\CargoPersona $cargoPersona = null)
    {
        $this->cargoPersona = $cargoPersona;

        return $this;
    }

    /**
     * Get cargoPersona
     *
     * @return \App\Entity\CargoPersona
     */
    public function getCargoPersona()
    {
        return $this->cargoPersona;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return Iniciador
     */
    public function setCreadoPor(\App\Entity\Usuario $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \App\Entity\Usuario $actualizadoPor
     *
     * @return Iniciador
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
