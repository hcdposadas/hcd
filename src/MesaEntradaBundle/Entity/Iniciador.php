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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CargoPersona")
     * @ORM\JoinColumn(name="cargo_persona_id", referencedColumnName="id")
     */
    private $cargoPersona;

    public function __toString()
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
     * @param \AppBundle\Entity\CargoPersona $cargoPersona
     *
     * @return Iniciador
     */
    public function setCargoPersona(\AppBundle\Entity\CargoPersona $cargoPersona = null)
    {
        $this->cargoPersona = $cargoPersona;

        return $this;
    }

    /**
     * Get cargoPersona
     *
     * @return \AppBundle\Entity\CargoPersona
     */
    public function getCargoPersona()
    {
        return $this->cargoPersona;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return Iniciador
     */
    public function setCreadoPor(\UsuariosBundle\Entity\Usuario $creadoPor = null)
    {
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
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
