<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 * @ORM\Table(name="fos_user")
 */
class Usuario implements UserInterface {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json", nullable=true)
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Persona")
	 * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", nullable=true)
	 */
	private $persona;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $enabled;

	public function __toString(): string {
		return $this->username;
	}

	public function esIniciador() {
		$return = false;

		foreach ( $this->getPersona()->getCargoPersona() as $cargoPersona ) {
			if ( $cargoPersona->getIniciador() ) {
				$return = true;
			}
		}

		return $return;
	}

	public function getIniciador() {
		$return = false;

		foreach ( $this->getPersona()->getCargoPersona() as $cargoPersona ) {
			if ( $cargoPersona->getIniciador() ) {
				$return = $cargoPersona->getIniciador();
			}
		}

		return $return;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail( string $email ): self {
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUsername(): string {
		return (string) $this->username;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique( $roles );
	}

	public function setRoles( array $roles ): self {
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string {
		return (string) $this->password;
	}

	public function setPassword( string $password ): self {
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt() {
		// not needed when using the "bcrypt" algorithm in security.yaml
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials() {
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	/**
	 * Set persona
	 *
	 * @param \App\Entity\Persona $persona
	 *
	 * @return Usuario
	 */
	public function setPersona( \App\Entity\Persona $persona = null ) {
		$this->persona = $persona;

		return $this;
	}

	/**
	 * Get persona
	 *
	 * @return \App\Entity\Persona
	 */
	public function getPersona() {
		return $this->persona;
	}

	public function setUsername( string $username ): self {
		$this->username = $username;

		return $this;
	}

	public function getEnabled(): ?bool {
		return $this->enabled;
	}

	public function setEnabled( ?bool $enabled ): self {
		$this->enabled = $enabled;

		return $this;
	}
}
