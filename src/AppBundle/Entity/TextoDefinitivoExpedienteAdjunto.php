<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * TextoDefinitivoExpedienteAdjunto
 *
 * @ORM\Table(name="texto_definitivo_expediente_adjunto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextoDefinitivoExpedienteAdjuntoRepository")
 */
class TextoDefinitivoExpedienteAdjunto extends BaseClass
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
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\TextoDefinitivo", inversedBy="expedientesAdjuntos")
	 * @ORM\JoinColumn(name="texto_definitivo_id", referencedColumnName="id")
	 */
	private $textoDefinitivo;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
	 */
	private $expediente;

	public function __toString() {
		return $this->expediente->__toString();
	}


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
     * @return TextoDefinitivoExpedienteAdjunto
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
     * @return TextoDefinitivoExpedienteAdjunto
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set textoDefinitivo
     *
     * @param \MesaEntradaBundle\Entity\TextoDefinitivo $textoDefinitivo
     *
     * @return TextoDefinitivoExpedienteAdjunto
     */
    public function setTextoDefinitivo(\MesaEntradaBundle\Entity\TextoDefinitivo $textoDefinitivo = null)
    {
        $this->textoDefinitivo = $textoDefinitivo;

        return $this;
    }

    /**
     * Get textoDefinitivo
     *
     * @return \MesaEntradaBundle\Entity\TextoDefinitivo
     */
    public function getTextoDefinitivo()
    {
        return $this->textoDefinitivo;
    }

    /**
     * Set expediente
     *
     * @param \MesaEntradaBundle\Entity\Expediente $expediente
     *
     * @return TextoDefinitivoExpedienteAdjunto
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
     * @return TextoDefinitivoExpedienteAdjunto
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
     * @return TextoDefinitivoExpedienteAdjunto
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
