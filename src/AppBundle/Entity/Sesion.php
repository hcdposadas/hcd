<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Sesion
 *
 * @ORM\Table(name="sesion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SesionRepository")
 */
class Sesion extends BaseClass
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
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="orden_del_dia", type="text")
	 */
	private $ordenDelDia;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="asuntos_entrados", type="text")
	 */
	private $asuntosEntrados;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="acta", type="text", nullable=true)
	 */
	private $acta;




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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Sesion
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Sesion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set ordenDelDia
     *
     * @param string $ordenDelDia
     *
     * @return Sesion
     */
    public function setOrdenDelDia($ordenDelDia)
    {
        $this->ordenDelDia = $ordenDelDia;

        return $this;
    }

    /**
     * Get ordenDelDia
     *
     * @return string
     */
    public function getOrdenDelDia()
    {
        return $this->ordenDelDia;
    }

    /**
     * Set asuntosEntrados
     *
     * @param string $asuntosEntrados
     *
     * @return Sesion
     */
    public function setAsuntosEntrados($asuntosEntrados)
    {
        $this->asuntosEntrados = $asuntosEntrados;

        return $this;
    }

    /**
     * Get asuntosEntrados
     *
     * @return string
     */
    public function getAsuntosEntrados()
    {
        return $this->asuntosEntrados;
    }

    /**
     * Set acta
     *
     * @param string $acta
     *
     * @return Sesion
     */
    public function setActa($acta)
    {
        $this->acta = $acta;

        return $this;
    }

    /**
     * Get acta
     *
     * @return string
     */
    public function getActa()
    {
        return $this->acta;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Sesion
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
     * @return Sesion
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return Sesion
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
     * @return Sesion
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
