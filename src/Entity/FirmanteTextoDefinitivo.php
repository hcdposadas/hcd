<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FirmanteTextoDefinitivo
 *
 * @ORM\Table(name="firmante_texto_definitivo")
 * @ORM\Entity()
 * @UniqueEntity(
 *     fields={"iniciador", "textoDefinitivo"},
 *     errorPath="iniciador",
 *     message="El firmante está duplicado"
 * )
 */
class FirmanteTextoDefinitivo extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Iniciador")
	 * @ORM\JoinColumn(name="iniciador_id", referencedColumnName="id")
	 */
	private $iniciador;


	/**
	 * @var bool
	 *
	 * @ORM\Column(name="presidente", type="boolean", options={"default":false})
	 */
	private $presidente = false;

	/**
	 * @ORM\ManyToOne(targetEntity=TextoDefinitivo::class, inversedBy="firmantes")
	 */
	private $textoDefinitivo;


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Set presidente
	 *
	 * @param boolean $presidente
	 *
	 * @return FirmanteDictamen
	 */
	public function setPresidente( $presidente ) {
		$this->presidente = $presidente;

		return $this;
	}

	/**
	 * Get presidente
	 *
	 * @return boolean
	 */
	public function getPresidente() {
		return $this->presidente;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return FirmanteDictamen
	 */
	public function setFechaCreacion( $fechaCreacion ) {
		$this->fechaCreacion = $fechaCreacion;

		return $this;
	}

	/**
	 * Set fechaActualizacion
	 *
	 * @param \DateTime $fechaActualizacion
	 *
	 * @return FirmanteDictamen
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}


	/**
	 * Set iniciador
	 *
	 * @param \App\Entity\Iniciador $iniciador
	 *
	 * @return FirmanteDictamen
	 */
	public function setIniciador( \App\Entity\Iniciador $iniciador = null ) {
		$this->iniciador = $iniciador;

		return $this;
	}

	/**
	 * Get iniciador
	 *
	 * @return \App\Entity\Iniciador
	 */
	public function getIniciador() {
		return $this->iniciador;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return FirmanteDictamen
	 */
	public function setCreadoPor( \App\Entity\Usuario $creadoPor = null ) {
		$this->creadoPor = $creadoPor;

		return $this;
	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \App\Entity\Usuario $actualizadoPor
	 *
	 * @return FirmanteDictamen
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	public function getTextoDefinitivo(): ?TextoDefinitivo {
		return $this->textoDefinitivo;
	}

	public function setTextoDefinitivo( ?TextoDefinitivo $textoDefinitivo ): self {
		$this->textoDefinitivo = $textoDefinitivo;

		return $this;
	}
}
