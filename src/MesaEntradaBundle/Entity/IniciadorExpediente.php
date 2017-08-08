<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * IniciadorExpediente
 *
 * @ORM\Table(name="iniciador_expediente")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\IniciadorExpedienteRepository")
 */
class IniciadorExpediente extends BaseClass
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
     * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente", inversedBy="iniciadores")
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
     */
    private $expediente;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Iniciador")
     * @ORM\JoinColumn(name="iniciador_id", referencedColumnName="id")
     */
    private $iniciador;


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
     * @return IniciadorExpediente
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
     * @return IniciadorExpediente
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
     * @return IniciadorExpediente
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
     * Set iniciador
     *
     * @param \MesaEntradaBundle\Entity\Iniciador $iniciador
     *
     * @return IniciadorExpediente
     */
    public function setIniciador(\MesaEntradaBundle\Entity\Iniciador $iniciador = null)
    {
        $this->iniciador = $iniciador;

        return $this;
    }

    /**
     * Get iniciador
     *
     * @return \MesaEntradaBundle\Entity\Iniciador
     */
    public function getIniciador()
    {
        return $this->iniciador;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return IniciadorExpediente
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
     * @return IniciadorExpediente
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
