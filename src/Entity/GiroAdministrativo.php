<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * Giro
 *
 * @ORM\Table(name="giro_administrativo")
 * @ORM\Entity(repositoryClass="App\Repository\GiroAdministrativoRepository")
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_giro", type="date")
	 */
	private $fechaGiro;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_origen_id", referencedColumnName="id")
	 */
	private $areaOrigen;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\AreaAdministrativa")
	 * @ORM\JoinColumn(name="area_destino_id", referencedColumnName="id")
	 */
	private $areaDestino;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Expediente", inversedBy="giroAdministrativos")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
	 */
	private $expediente;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="texto", type="text", nullable=true)
	 */
	private $texto;




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
     * @param \App\Entity\AreaAdministrativa $areaOrigen
     *
     * @return GiroAdministrativo
     */
    public function setAreaOrigen(\App\Entity\AreaAdministrativa $areaOrigen = null)
    {
        $this->areaOrigen = $areaOrigen;

        return $this;
    }

    /**
     * Get areaOrigen
     *
     * @return \App\Entity\AreaAdministrativa
     */
    public function getAreaOrigen()
    {
        return $this->areaOrigen;
    }

    /**
     * Set areaDestino
     *
     * @param \App\Entity\AreaAdministrativa $areaDestino
     *
     * @return GiroAdministrativo
     */
    public function setAreaDestino(\App\Entity\AreaAdministrativa $areaDestino = null)
    {
        $this->areaDestino = $areaDestino;

        return $this;
    }

    /**
     * Get areaDestino
     *
     * @return \App\Entity\AreaAdministrativa
     */
    public function getAreaDestino()
    {
        return $this->areaDestino;
    }

    /**
     * Set expediente
     *
     * @param \App\Entity\Expediente $expediente
     *
     * @return GiroAdministrativo
     */
    public function setExpediente(\App\Entity\Expediente $expediente = null)
    {
        $this->expediente = $expediente;

        return $this;
    }

    /**
     * Get expediente
     *
     * @return \App\Entity\Expediente
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return GiroAdministrativo
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
     * @return GiroAdministrativo
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return GiroAdministrativo
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }
}
