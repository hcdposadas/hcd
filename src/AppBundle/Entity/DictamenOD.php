<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * DictamenOD
 *
 * @ORM\Table(name="dictamen_o_d")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DictamenODRepository")
 */
class DictamenOD extends BaseClass
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
	 * @var Expediente $expediente
	 *
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
	 */
	private $expediente;

	/**
	 * @var OrdenDelDia $ordenDelDia
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OrdenDelDia", inversedBy="dictamenes")
	 * @ORM\JoinColumn(name="orden_del_dia_id", referencedColumnName="id", nullable=true)
	 */
	private $ordenDelDia;


    /**
     * Get id
     *
     * @return int
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
     * @return DictamenOD
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
     * @return DictamenOD
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set expediente
     *
     * @param \MesaEntradaBundle\Entity\Expediente $expediente
     *
     * @return DictamenOD
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
     * Set ordenDelDia
     *
     * @param \AppBundle\Entity\OrdenDelDia $ordenDelDia
     *
     * @return DictamenOD
     */
    public function setOrdenDelDia(\AppBundle\Entity\OrdenDelDia $ordenDelDia = null)
    {
        $this->ordenDelDia = $ordenDelDia;

        return $this;
    }

    /**
     * Get ordenDelDia
     *
     * @return \AppBundle\Entity\OrdenDelDia
     */
    public function getOrdenDelDia()
    {
        return $this->ordenDelDia;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return DictamenOD
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
     * @return DictamenOD
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
