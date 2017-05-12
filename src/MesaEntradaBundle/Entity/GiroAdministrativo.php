<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Giro
 *
 * @ORM\Table(name="giro_administrativo")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\GiroAdministrativoRepository")
 */
class GiroAdministrativo extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="cabecera", type="boolean", nullable=true)
	 */
	private $cabecera;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fechaGiro", type="date")
	 */
	private $fechaGiro;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_origen_id", referencedColumnName="id")
	 */
	private $areaOrigen;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_destino_id", referencedColumnName="id")
	 */
	private $areaDestino;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente", inversedBy="giroAdministrativos")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
	 */
	private $expediente;




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
     * Set cabecera
     *
     * @param boolean $cabecera
     *
     * @return GiroAdministrativo
     */
    public function setCabecera($cabecera)
    {
        $this->cabecera = $cabecera;

        return $this;
    }

    /**
     * Get cabecera
     *
     * @return boolean
     */
    public function getCabecera()
    {
        return $this->cabecera;
    }

    /**
     * Set fechaGiro
     *
     * @param \DateTime $fechaGiro
     *
     * @return GiroAdministrativo
     */
    public function setFechaGiro($fechaGiro)
    {
        $this->fechaGiro = $fechaGiro;

        return $this;
    }

    /**
     * Get fechaGiro
     *
     * @return \DateTime
     */
    public function getFechaGiro()
    {
        return $this->fechaGiro;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return GiroAdministrativo
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
     * @return GiroAdministrativo
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set areaOrigen
     *
     * @param \AppBundle\Entity\AreaAdministrativa $areaOrigen
     *
     * @return GiroAdministrativo
     */
    public function setAreaOrigen(\AppBundle\Entity\AreaAdministrativa $areaOrigen = null)
    {
        $this->areaOrigen = $areaOrigen;

        return $this;
    }

    /**
     * Get areaOrigen
     *
     * @return \AppBundle\Entity\AreaAdministrativa
     */
    public function getAreaOrigen()
    {
        return $this->areaOrigen;
    }

    /**
     * Set areaDestino
     *
     * @param \AppBundle\Entity\AreaAdministrativa $areaDestino
     *
     * @return GiroAdministrativo
     */
    public function setAreaDestino(\AppBundle\Entity\AreaAdministrativa $areaDestino = null)
    {
        $this->areaDestino = $areaDestino;

        return $this;
    }

    /**
     * Get areaDestino
     *
     * @return \AppBundle\Entity\AreaAdministrativa
     */
    public function getAreaDestino()
    {
        return $this->areaDestino;
    }

    /**
     * Set expediente
     *
     * @param \MesaEntradaBundle\Entity\Expediente $expediente
     *
     * @return GiroAdministrativo
     */
    public function setExpediente(\MesaEntradaBundle\Entity\Expediente $expediente = null)
    {
        $this->expediente = $expediente;

        return $this;
    }

    /**
     * Get expediente
     *
     * @return \MesaEntradaBundle\Entity\Expediente
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return GiroAdministrativo
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
     * @return GiroAdministrativo
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
