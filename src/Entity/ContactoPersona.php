<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * ContactoPersona
 *
 * @ORM\Table(name="contacto_persona")
 * @ORM\Entity(repositoryClass="App\Repository\ContactoPersonaRepository")
 */
class ContactoPersona extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Persona", inversedBy="contactoPersona", cascade={"persist"})
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
	 */
	private $persona;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Contacto")
	 * @ORM\JoinColumn(name="contacto_id", referencedColumnName="id")
	 */
	private $contacto;



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
     * @return ContactoPersona
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
     * @return ContactoPersona
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set persona
     *
     * @param \App\Entity\Persona $persona
     *
     * @return ContactoPersona
     */
    public function setPersona(\App\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \App\Entity\Persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set contacto
     *
     * @param \App\Entity\Contacto $contacto
     *
     * @return ContactoPersona
     */
    public function setContacto(\App\Entity\Contacto $contacto = null)
    {
        $this->contacto = $contacto;

        return $this;
    }

    /**
     * Get contacto
     *
     * @return \App\Entity\Contacto
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return ContactoPersona
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
     * @return ContactoPersona
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
