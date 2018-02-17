<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TipoProyecto
 *
 * @ORM\Table(name="tipo_proyecto")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\TipoProyectoRepository")
 */
class TipoProyecto extends BaseClass
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="texto_por_defecto", type="text", nullable=true)
     */
    private $textoPorDefecto;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slug", type="string", length=255, nullable=true)
	 * @Gedmo\Slug(fields={"nombre"})
	 */
	private $slug;



	public function __toString() {
		return $this->nombre;
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return TipoProyecto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return TipoProyecto
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return TipoProyecto
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
     * @return TipoProyecto
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
     * @return TipoProyecto
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
     * @return TipoProyecto
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set textoPorDefecto
     *
     * @param string $textoPorDefecto
     *
     * @return TipoProyecto
     */
    public function setTextoPorDefecto($textoPorDefecto)
    {
        $this->textoPorDefecto = $textoPorDefecto;

        return $this;
    }

    /**
     * Get textoPorDefecto
     *
     * @return string
     */
    public function getTextoPorDefecto()
    {
        return $this->textoPorDefecto;
    }
}
