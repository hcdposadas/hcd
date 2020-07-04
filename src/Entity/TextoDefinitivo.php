<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * TextoDefinitivo
 *
 * @ORM\Table(name="texto_definitivo")
 * @ORM\Entity(repositoryClass="App\Repository\TextoDefinitivoRepository")
 */
class TextoDefinitivo extends BaseClass {
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
	 * @ORM\Column(name="texto", type="text")
	 */
	private $texto;

	/**
	 * @ORM\Column(name="numero", type="string", length=255, nullable=true)
	 * @var string
	 */
	private $numero;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\AnexoTextoDefinitivo", mappedBy="textoDefinitivo", cascade={"persist", "remove"}, orphanRemoval=true)
	 *
	 */
	private $anexos;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Dictamen", inversedBy="textosDefinitivos")
	 * @ORM\JoinColumn(name="dictamen_id", referencedColumnName="id")
	 */
	private $dictamen;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Rama")
	 * @ORM\JoinColumn(name="rama_id", referencedColumnName="id", nullable=true)
	 */
	private $rama;

	/**
	 * @ORM\OneToMany(targetEntity=TextoDefinitivoExpedienteAdjunto::class, mappedBy="textoDefinitivo", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	private $expedientesAdjuntos;

	/**
	 * @ORM\ManyToOne(targetEntity=Sesion::class)
	 * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
	 */
	private $aprobadoEnSesion;


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->anexos              = new \Doctrine\Common\Collections\ArrayCollection();
		$this->expedientesAdjuntos = new ArrayCollection();
	}

	/**
	 * Set texto
	 *
	 * @param string $texto
	 *
	 * @return TextoDefinitivo
	 */
	public function setTexto( $texto ) {
		$this->texto = $texto;

		return $this;
	}

	/**
	 * Get texto
	 *
	 * @return string
	 */
	public function getTexto() {
		return $this->texto;
	}

	/**
	 * Set numero
	 *
	 * @param string $numero
	 *
	 * @return TextoDefinitivo
	 */
	public function setNumero( $numero ) {
		$this->numero = $numero;

		return $this;
	}

	/**
	 * Get numero
	 *
	 * @return string
	 */
	public function getNumero() {
		return $this->numero;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return TextoDefinitivo
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
	 * @return TextoDefinitivo
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Add anexo
	 *
	 * @param \App\Entity\AnexoTextoDefinitivo $anexo
	 *
	 * @return TextoDefinitivo
	 */
	public function addAnexo( \App\Entity\AnexoTextoDefinitivo $anexo ) {
		$this->anexos[] = $anexo;

		return $this;
	}

	/**
	 * Remove anexo
	 *
	 * @param \App\Entity\AnexoTextoDefinitivo $anexo
	 */
	public function removeAnexo( \App\Entity\AnexoTextoDefinitivo $anexo ) {
		$this->anexos->removeElement( $anexo );
	}

	/**
	 * Get anexos
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getAnexos() {
		return $this->anexos;
	}

	/**
	 * Set dictamen
	 *
	 * @param \App\Entity\Dictamen $dictamen
	 *
	 * @return TextoDefinitivo
	 */
	public function setDictamen( \App\Entity\Dictamen $dictamen = null ) {
		$this->dictamen = $dictamen;

		return $this;
	}

	/**
	 * Get dictamen
	 *
	 * @return \App\Entity\Dictamen
	 */
	public function getDictamen() {
		return $this->dictamen;
	}

	/**
	 * Set rama
	 *
	 * @param \App\Entity\Rama $rama
	 *
	 * @return TextoDefinitivo
	 */
	public function setRama( \App\Entity\Rama $rama = null ) {
		$this->rama = $rama;

		return $this;
	}

	/**
	 * Get rama
	 *
	 * @return \App\Entity\Rama
	 */
	public function getRama() {
		return $this->rama;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return TextoDefinitivo
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
	 * @return TextoDefinitivo
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * @return Collection|TextoDefinitivoExpedienteAdjunto[]
	 */
	public function getExpedientesAdjuntos(): Collection {
		return $this->expedientesAdjuntos;
	}

	public function addExpedientesAdjunto( TextoDefinitivoExpedienteAdjunto $expedientesAdjunto ): self {
		if ( ! $this->expedientesAdjuntos->contains( $expedientesAdjunto ) ) {
			$this->expedientesAdjuntos[] = $expedientesAdjunto;
			$expedientesAdjunto->setTextoDefinitivo( $this );
		}

		return $this;
	}

	public function removeExpedientesAdjunto( TextoDefinitivoExpedienteAdjunto $expedientesAdjunto ): self {
		if ( $this->expedientesAdjuntos->contains( $expedientesAdjunto ) ) {
			$this->expedientesAdjuntos->removeElement( $expedientesAdjunto );
			// set the owning side to null (unless already changed)
			if ( $expedientesAdjunto->getTextoDefinitivo() === $this ) {
				$expedientesAdjunto->setTextoDefinitivo( null );
			}
		}

		return $this;
	}

	public function getAprobadoEnSesion(): ?Sesion {
		return $this->aprobadoEnSesion;
	}

	public function setAprobadoEnSesion( ?Sesion $aprobadoEnSesion ): self {
		$this->aprobadoEnSesion = $aprobadoEnSesion;

		return $this;
	}
}
