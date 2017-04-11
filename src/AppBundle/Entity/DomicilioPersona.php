<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * DomicilioPersona
 *
 * @ORM\Table(name="domicilio_persona")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DomicilioPersonaRepository")
 */
class DomicilioPersona extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona", inversedBy="domicilioPersona", cascade={"persist"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Domicilio", cascade={"persist"})
	 * @ORM\JoinColumn(name="domicilio_id", referencedColumnName="id")
	 */
	private $domicilio;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

    /**
     * Set persona
     *
     * @param \AppBundle\Entity\Persona $persona
     * @return DomicilioPersona
     */
    public function setPersona(\AppBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \AppBundle\Entity\Persona 
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set domicilio
     *
     * @param \AppBundle\Entity\Domicilio $domicilio
     * @return DomicilioPersona
     */
    public function setDomicilio(\AppBundle\Entity\Domicilio $domicilio = null)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return \AppBundle\Entity\Domicilio 
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return DomicilioPersona
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
     * @return DomicilioPersona
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
     * @return DomicilioPersona
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
     * @return DomicilioPersona
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
