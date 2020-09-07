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

	const TIPO_DOCUMENTO_ACTA = 'Acta';
	const TIPO_DOCUMENTO_DECRETO = 'Decreto';

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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Dictamen", inversedBy="textosDefinitivos", cascade={"persist", "remove"})
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
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tituloAnexo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $tipoDocumento;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $numeroDocumento;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $fechaDocumento;

	/**
	 * @ORM\ManyToOne(targetEntity=TipoProyecto::class)
	 */
	private $tipoTextoDefinitivo;

	/**
	 * @ORM\OneToMany(targetEntity=FirmanteTextoDefinitivo::class, mappedBy="textoDefinitivo", cascade={"persist"})
	 */
	private $firmantes;


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
		$this->firmantes           = new ArrayCollection();
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
		$anexo->setTextoDefinitivo( $this );

		$this->anexos->add( $anexo );

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

	public function getTituloAnexo(): ?string {
		return $this->tituloAnexo;
	}

	public function setTituloAnexo( ?string $tituloAnexo ): self {
		$this->tituloAnexo = $tituloAnexo;

		return $this;
	}

	public function getTipoDocumento(): ?string {
		return $this->tipoDocumento;
	}

	public function setTipoDocumento( ?string $tipoDocumento ): self {
		$this->tipoDocumento = $tipoDocumento;

		return $this;
	}

	public function getNumeroDocumento(): ?string {
		return $this->numeroDocumento;
	}

	public function setNumeroDocumento( ?string $numeroDocumento ): self {
		$this->numeroDocumento = $numeroDocumento;

		return $this;
	}

	public function getFechaDocumento(): ?\DateTimeInterface {
		return $this->fechaDocumento;
	}

	public function setFechaDocumento( ?\DateTimeInterface $fechaDocumento ): self {
		$this->fechaDocumento = $fechaDocumento;

		return $this;
	}

	public function getTipoTextoDefinitivo(): ?TipoProyecto {
		return $this->tipoTextoDefinitivo;
	}

	public function setTipoTextoDefinitivo( ?TipoProyecto $tipoTextoDefinitivo ): self {
		$this->tipoTextoDefinitivo = $tipoTextoDefinitivo;

		return $this;
	}

	/**
	 * @return Collection|FirmanteTextoDefinitivo[]
	 */
	public function getFirmantes(): Collection {
		return $this->firmantes;
	}

	public function addFirmante( FirmanteTextoDefinitivo $firmante ): self {
		if ( ! $this->firmantes->contains( $firmante ) ) {
			$this->firmantes[] = $firmante;
			$firmante->setTextoDefinitivo( $this );
		}

		return $this;
	}

	public function removeFirmante( FirmanteTextoDefinitivo $firmante ): self {
		if ( $this->firmantes->contains( $firmante ) ) {
			$this->firmantes->removeElement( $firmante );
			// set the owning side to null (unless already changed)
			if ( $firmante->getTextoDefinitivo() === $this ) {
				$firmante->setTextoDefinitivo( null );
			}
		}

		return $this;
	}
}
