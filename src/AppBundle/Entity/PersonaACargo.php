<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * PersonaACargo
 *
 * @ORM\Table(name="persona_a_cargo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonaACargoRepository")
 */
class PersonaACargo extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona", inversedBy="personaACargo", cascade={"persist"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoRelacionPersona")
	 * @ORM\JoinColumn(name="tipo_relacion_id", referencedColumnName="id")
	 */
	private $tipoRelacionPersona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona")
	 * @ORM\JoinColumn(name="persona_a_cargo_id", referencedColumnName="id")
	 */
	private $personaACargo;


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
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return PersonaACargo
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
     * @return PersonaACargo
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set persona
     *
     * @param \AppBundle\Entity\Persona $persona
     *
     * @return PersonaACargo
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
     * Set tipoRelacionPersona
     *
     * @param \AppBundle\Entity\TipoRelacionPersona $tipoRelacionPersona
     *
     * @return PersonaACargo
     */
    public function setTipoRelacionPersona(\AppBundle\Entity\TipoRelacionPersona $tipoRelacionPersona = null)
    {
        $this->tipoRelacionPersona = $tipoRelacionPersona;

        return $this;
    }

    /**
     * Get tipoRelacionPersona
     *
     * @return \AppBundle\Entity\TipoRelacionPersona
     */
    public function getTipoRelacionPersona()
    {
        return $this->tipoRelacionPersona;
    }

    /**
     * Set personaACargo
     *
     * @param \AppBundle\Entity\Persona $personaACargo
     *
     * @return PersonaACargo
     */
    public function setPersonaACargo(\AppBundle\Entity\Persona $personaACargo = null)
    {
        $this->personaACargo = $personaACargo;

        return $this;
    }

    /**
     * Get personaACargo
     *
     * @return \AppBundle\Entity\Persona
     */
    public function getPersonaACargo()
    {
        return $this->personaACargo;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return PersonaACargo
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
     * @return PersonaACargo
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
