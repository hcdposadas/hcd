<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use Doctrine\ORM\Mapping as ORM;

/**
 * TextoDefinitivoExpedienteAdjunto
 *
 * @ORM\Table(name="texto_definitivo_expediente_adjunto")
 * @ORM\Entity(repositoryClass="App\Repository\TextoDefinitivoExpedienteAdjuntoRepository")
 */
class TextoDefinitivoExpedienteAdjunto extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Expediente")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
	 */
	private $expediente;

	/**
	 * @ORM\ManyToOne(targetEntity=TextoDefinitivo::class, inversedBy="expedientesAdjuntos")
	 * @ORM\JoinColumn(name="texto_definitivo_id", referencedColumnName="id")
	 */
	private $textoDefinitivo;

	public function __toString(): ?string {
		return $this->expediente->__toString();
	}


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return TextoDefinitivoExpedienteAdjunto
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
	 * @return TextoDefinitivoExpedienteAdjunto
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set expediente
	 *
	 * @param \App\Entity\Expediente $expediente
	 *
	 * @return TextoDefinitivoExpedienteAdjunto
	 */
	public function setExpediente( \App\Entity\Expediente $expediente = null ) {
		$this->expediente = $expediente;

		return $this;
	}

	/**
	 * Get expediente
	 *
	 * @return \App\Entity\Expediente
	 */
	public function getExpediente() {
		return $this->expediente;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return TextoDefinitivoExpedienteAdjunto
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
	 * @return TextoDefinitivoExpedienteAdjunto
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
