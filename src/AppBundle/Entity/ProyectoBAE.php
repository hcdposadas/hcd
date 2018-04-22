<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * ProyectoBAE
 *
 * @ORM\Table(name="proyecto_b_a_e")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProyectoBAERepository")
 */
class ProyectoBAE extends BaseClass
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
	 * @var BoletinAsuntoEntrado $boletinAsuntoEntrado
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BoletinAsuntoEntrado", inversedBy="proyectos")
	 * @ORM\JoinColumn(name="boletin_asunto_entrado_id", referencedColumnName="id", nullable=true)
	 */
	private $boletinAsuntoEntrado;


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
     * @return ProyectoBAE
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
     * @return ProyectoBAE
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
     * @return ProyectoBAE
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
     * Set boletinAsuntoEntrado
     *
     * @param \AppBundle\Entity\BoletinAsuntoEntrado $boletinAsuntoEntrado
     *
     * @return ProyectoBAE
     */
    public function setBoletinAsuntoEntrado(\AppBundle\Entity\BoletinAsuntoEntrado $boletinAsuntoEntrado = null)
    {
        $this->boletinAsuntoEntrado = $boletinAsuntoEntrado;

        return $this;
    }

    /**
     * Get boletinAsuntoEntrado
     *
     * @return \AppBundle\Entity\BoletinAsuntoEntrado
     */
    public function getBoletinAsuntoEntrado()
    {
        return $this->boletinAsuntoEntrado;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return ProyectoBAE
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
     * @return ProyectoBAE
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
