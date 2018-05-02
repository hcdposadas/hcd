<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * BoletinAsuntoEntrado
 *
 * @ORM\Table(name="boletin_asuntos_entrados")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BoletinAsuntoEntradoRepository")
 */
class BoletinAsuntoEntrado extends BaseClass
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
	 * @var Sesion $sesion
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sesion", inversedBy="bae")
	 * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
	 */
	private $sesion;

	/**
	 * @var ProyectoBAE[]
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProyectoBAE", mappedBy="boletinAsuntoEntrado", cascade={"persist", "remove"})
	 *
	 */
	private $proyectos;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="cerrado", type="boolean")
	 */
	private $cerrado;



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
     * Set cerrado
     *
     * @param boolean $cerrado
     *
     * @return BoletinAsuntoEntrado
     */
    public function setCerrado($cerrado)
    {
        $this->cerrado = $cerrado;

        return $this;
    }

    /**
     * Get cerrado
     *
     * @return boolean
     */
    public function getCerrado()
    {
        return $this->cerrado;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return BoletinAsuntoEntrado
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
     * @return BoletinAsuntoEntrado
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set sesion
     *
     * @param \AppBundle\Entity\Sesion $sesion
     *
     * @return BoletinAsuntoEntrado
     */
    public function setSesion(\AppBundle\Entity\Sesion $sesion = null)
    {
        $this->sesion = $sesion;

        return $this;
    }

    /**
     * Get sesion
     *
     * @return \AppBundle\Entity\Sesion
     */
    public function getSesion()
    {
        return $this->sesion;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return BoletinAsuntoEntrado
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
     * @return BoletinAsuntoEntrado
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proyectos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add proyecto
     *
     * @param \AppBundle\Entity\ProyectoBAE $proyecto
     *
     * @return BoletinAsuntoEntrado
     */
    public function addProyecto(\AppBundle\Entity\ProyectoBAE $proyecto)
    {
    	$proyecto->setBoletinAsuntoEntrado($this);

        $this->proyectos->add($proyecto);

        return $this;
    }

    /**
     * Remove proyecto
     *
     * @param \AppBundle\Entity\ProyectoBAE $proyecto
     */
    public function removeProyecto(\AppBundle\Entity\ProyectoBAE $proyecto)
    {
        $this->proyectos->removeElement($proyecto);
    }

    /**
     * Get proyectos
     *
     * @return \Doctrine\Common\Collections\Collection|ProyectoBAE[]
     */
    public function getProyectos()
    {
        return $this->proyectos;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ProyectoBAE[]
     */
    public function getProyectosDeConcejales()
    {
        return $this->proyectos->filter(function (ProyectoBAE $proyectoBae) {
            return $proyectoBae->getExpediente()->esProyectoDeConcejal();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ProyectoBAE[]
     */
    public function getProyectosDeDEM()
    {
        return $this->proyectos->filter(function (ProyectoBAE $proyectoBae) {
            return $proyectoBae->getExpediente()->esProyectoDeDEM();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ProyectoBAE[]
     */
    public function getProyectosDeDefensor()
    {
        return $this->proyectos->filter(function (ProyectoBAE $proyectoBae) {
            return $proyectoBae->getExpediente()->esProyectoDeDefensor();
        });
    }
}
