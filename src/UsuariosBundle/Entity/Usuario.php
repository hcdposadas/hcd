<?php

namespace UsuariosBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class Usuario extends BaseUser {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona")
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", nullable=true)
	 */
	private $persona;

	public function esIniciador() {
		$return= false;

		foreach ( $this->getPersona()->getCargoPersona() as $cargoPersona ) {
			if ($cargoPersona->getIniciador()){
				$return = true;
			}
		}

		return $return;
	}

	public function getIniciador() {
		$return= false;

		foreach ( $this->getPersona()->getCargoPersona() as $cargoPersona ) {
			if ($cargoPersona->getIniciador()){
				$return = $cargoPersona->getIniciador();
			}
		}

		return $return;
	}

	public function __construct() {
		parent::__construct();
		// your own logic
	}


    /**
     * Set persona
     *
     * @param \AppBundle\Entity\Persona $persona
     *
     * @return Usuario
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
}
