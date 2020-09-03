<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Expediente;
use App\Entity\Base\BaseClass;

/**
 * Decreto
 *
 * @ORM\Table(name="decreto")
 * @ORM\Entity(repositoryClass="App\Repository\DecretoRepository")
 */
class Decreto extends BaseClass
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
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="cuerpo", type="text", nullable=true)
     */
    private $cuerpo;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

	/**
	 * @var Expediente $expediente
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Expediente")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
	 */
	private $expediente;

	/**
	 * @var TipoDecreto $tipoDecreto
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\TipoDecreto")
	 * @ORM\JoinColumn(name="tipo_decreto_id", referencedColumnName="id", nullable=true)
	 */
	private $tipoDecreto;

	public function __toString() {
		return $this->numero;
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Decreto
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set cuerpo
     *
     * @param string $cuerpo
     *
     * @return Decreto
     */
    public function setCuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    /**
     * Get cuerpo
     *
     * @return string
     */
    public function getCuerpo()
    {
        return $this->cuerpo;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Decreto
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Decreto
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
     * @return Decreto
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set expediente
     *
     * @param \App\Entity\Expediente $expediente
     *
     * @return Decreto
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
     * Set tipoDecreto
     *
     * @param \App\Entity\TipoDecreto $tipoDecreto
     *
     * @return Decreto
     */
    public function setTipoDecreto(\App\Entity\TipoDecreto $tipoDecreto = null)
    {
        $this->tipoDecreto = $tipoDecreto;

        return $this;
    }

    /**
     * Get tipoDecreto
     *
     * @return \App\Entity\TipoDecreto
     */
    public function getTipoDecreto()
    {
        return $this->tipoDecreto;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return Decreto
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
     * @return Decreto
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
