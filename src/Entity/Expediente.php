<?php

namespace App\Entity;

use App\Entity\AreaAdministrativa;
use App\Entity\BoletinAsuntoEntrado;
use App\Entity\Cargo;
use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Usuario;
use App\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Expediente
 *
 * @ORM\Table(name="expediente")
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"expediente", "periodoLegislativo"},
 *     errorPath="expediente",
 *     message="Ya existe el expediente en el año y con esta letra"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ExpedienteRepository")
 */
class Expediente extends BaseClass {
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
	 * @ORM\Column(name="texto_definitivo", type="text", nullable=true)
	 */
	private $textoDefinitivo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="extracto", type="text", nullable=true)
	 */
	private $extracto;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="extracto_temario", type="text", nullable=true)
	 */
	private $extractoTemario;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="extracto_dictamen", type="text", nullable=true)
	 */
	private $extractoDictamen;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="expediente", type="string", length=255, nullable=true)
	 */
	private $expediente;

	/**
	 * @ORM\Column(name="anio",type="string", length=255, nullable=true)
	 * @var string
	 */
	private $anio;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="letra", type="string", length=255, nullable=true)
	 */
	private $letra;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha", type="date", nullable=true)
	 */
	private $fecha;

	/**
	 * @ORM\Column(name="registro_municipal", type="string", length=255, nullable=true)
	 * @var string
	 */
	private $registroMunicipal;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\TipoExpediente")
	 * @ORM\JoinColumn(name="tipo_expediente_id", referencedColumnName="id")
	 */
	private $tipoExpediente;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\PeriodoLegislativo")
	 * @ORM\JoinColumn(name="periodo_legislativo_id", referencedColumnName="id")
	 */
	private $periodoLegislativo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_nota", type="string", length=255, nullable=true)
	 */
	private $numeroNota;


	/**
	 * @var IniciadorExpediente[]
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\IniciadorExpediente", mappedBy="expediente", cascade={"persist"}, orphanRemoval=true)
	 * @ORM\OrderBy({"fechaCreacion" = "ASC"})
	 *
	 */
	private $iniciadores;


	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Persona")
	 * @ORM\JoinColumn(name="iniciador_particular_id", referencedColumnName="id")
	 */
	private $iniciadorParticular;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Dependencia")
	 * @ORM\JoinColumn(name="dependencia_id", referencedColumnName="id")
	 */
	private $dependencia;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\GiroAdministrativo", mappedBy="expediente", cascade={"persist"})
	 *
	 */
	private $giroAdministrativos;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\Giro", mappedBy="expediente", cascade={"persist"})
	 *
	 */
	private $giros;

	/**
	 * @ORM\Column(name="sesion_numero", type="integer", nullable=true)
	 * @var string
	 */
	private $sesionNumero;

	/**
	 * @ORM\Column(name="sesion_anio", type="integer", nullable=true)
	 * @var string
	 */
	private $sesionAnio;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $expedienteInterno;

	/**
	 * @Vich\UploadableField(mapping="expedientes_internos", fileNameProperty="expedienteInterno")
	 * @var File
	 */
	private $expedienteInternoFile;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $expedienteExterno;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\TipoProyecto")
	 * @ORM\JoinColumn(name="tipo_proyecto_id", referencedColumnName="id")
	 */
	private $tipoProyecto;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="nota", type="boolean", options={"default" = false})
	 */
	private $nota = false;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="texto", type="text", nullable=true)
	 */
	private $texto;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="borrador", type="boolean", nullable=true)
	 */
	private $borrador;

	/**
	 * @ORM\Column(name="codigo_referencia", type="string", length=255, nullable=true)
	 * @var string
	 */
	private $codigoReferencia;

	/**
	 * @ORM\Column(name="numero_de_hojas", type="string", length=255, nullable=true)
	 * @var string
	 */
	private $numeroDeHojas;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\AnexoExpediente", mappedBy="expediente", cascade={"persist", "remove"}, orphanRemoval=true)
	 *
	 */
	private $anexos;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_presentacion", type="datetime", nullable=true)
	 */
	private $fechaPresentacion;

	/**
	 * @var Usuario $asignadoPor
	 *
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
	 * @ORM\JoinColumn(name="asignado_por_id", referencedColumnName="id", nullable=true)
	 */
	private $asignadoPor;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\Dictamen", mappedBy="expediente", cascade={"persist", "remove"})
	 *
	 */
	private $dictamenes;

	/**
	 * @var IniciadorExpediente[]
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\ExpedienteAdjunto", mappedBy="expediente", cascade={"persist"}, orphanRemoval=true)
	 *
	 */
	private $expedientesAdjunto;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="proyecto_dem", type="boolean", nullable=true)
	 */
	private $proyectoDem;

	/**
	 * @Vich\UploadableField(mapping="expedientes_externos", fileNameProperty="expedienteExterno")
	 * @var File
	 */
	private $expedienteExternoFile;

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return Expediente
	 */
	public function setExpedienteExternoFile( File $file = null ) {
		$this->expedienteExternoFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getExpedienteExternoFile() {
		return $this->expedienteExternoFile;
	}

	/**
	 * @param string $expedienteExterno
	 *
	 * @return Expediente
	 */
	public function setExpedienteExterno( $expedienteExterno ) {
		$this->expedienteExterno = $expedienteExterno;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getExpedienteExterno() {
		return $this->expedienteExterno;
	}

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return Expediente
	 */
	public function setExpedienteInternoFile( File $file = null ) {
		$this->expedienteInternoFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getExpedienteInternoFile() {
		return $this->expedienteInternoFile;
	}

	/**
	 * @param string $expedienteInterno
	 *
	 * @return Expediente
	 */
	public function setExpedienteInterno( $expedienteInterno ) {
		$this->expedienteInterno = $expedienteInterno;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getExpedienteInterno() {
		return $this->expedienteInterno;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		if ( $this->getPeriodoLegislativo() ) {

			$anio = $this->anio ? $this->anio : $this->getPeriodoLegislativo()->getAnio();
		} else {
			$anio = $this->anio ? $this->anio : '';
		}

		return $this->expediente . '-' . strtoupper( $this->letra ) . '-' . $anio;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->iniciadores         = new \Doctrine\Common\Collections\ArrayCollection();
		$this->giroAdministrativos = new \Doctrine\Common\Collections\ArrayCollection();
		$this->giros               = new \Doctrine\Common\Collections\ArrayCollection();
		$this->anexos              = new \Doctrine\Common\Collections\ArrayCollection();
		$this->expedientesAdjunto  = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set textoDefinitivo
	 *
	 * @param string $textoDefinitivo
	 *
	 * @return Expediente
	 */
	public function setTextoDefinitivo( $textoDefinitivo ) {
		$this->textoDefinitivo = $textoDefinitivo;

		return $this;
	}

	/**
	 * Get textoDefinitivo
	 *
	 * @return string
	 */
	public function getTextoDefinitivo() {
		return $this->textoDefinitivo;
	}

	/**
	 * Set extracto
	 *
	 * @param string $extracto
	 *
	 * @return Expediente
	 */
	public function setExtracto( $extracto ) {
		$this->extracto = $extracto;

		return $this;
	}

	/**
	 * Get extracto
	 *
	 * @return string
	 */
	public function getExtracto() {
		return $this->extracto;
	}

	/**
	 * Set expediente
	 *
	 * @param string $expediente
	 *
	 * @return Expediente
	 */
	public function setExpediente( $expediente ) {
		$this->expediente = $expediente;

		return $this;
	}

	/**
	 * Get expediente
	 *
	 * @return string
	 */
	public function getExpediente() {
		return $this->expediente;
	}

	/**
	 * Set anio
	 *
	 * @param string $anio
	 *
	 * @return Expediente
	 */
	public function setAnio( $anio ) {
		$this->anio = $anio;

		return $this;
	}

	/**
	 * Get anio
	 *
	 * @return string
	 */
	public function getAnio() {
		return $this->anio;
	}

	/**
	 * Set letra
	 *
	 * @param string $letra
	 *
	 * @return Expediente
	 */
	public function setLetra( $letra ) {
		$this->letra = $letra;

		return $this;
	}

	/**
	 * Get letra
	 *
	 * @return string
	 */
	public function getLetra() {
		return $this->letra;
	}

	/**
	 * Set fecha
	 *
	 * @param \DateTime $fecha
	 *
	 * @return Expediente
	 */
	public function setFecha( $fecha ) {
		$this->fecha = $fecha;

		return $this;
	}

	/**
	 * Get fecha
	 *
	 * @return \DateTime
	 */
	public function getFecha() {
		return $this->fecha;
	}

	/**
	 * Set registroMunicipal
	 *
	 * @param string $registroMunicipal
	 *
	 * @return Expediente
	 */
	public function setRegistroMunicipal( $registroMunicipal ) {
		$this->registroMunicipal = $registroMunicipal;

		return $this;
	}

	/**
	 * Get registroMunicipal
	 *
	 * @return string
	 */
	public function getRegistroMunicipal() {
		return $this->registroMunicipal;
	}

	/**
	 * Set sesionNumero
	 *
	 * @param integer $sesionNumero
	 *
	 * @return Expediente
	 */
	public function setSesionNumero( $sesionNumero ) {
		$this->sesionNumero = $sesionNumero;

		return $this;
	}

	/**
	 * Get sesionNumero
	 *
	 * @return integer
	 */
	public function getSesionNumero() {
		return $this->sesionNumero;
	}

	/**
	 * Set sesionAnio
	 *
	 * @param integer $sesionAnio
	 *
	 * @return Expediente
	 */
	public function setSesionAnio( $sesionAnio ) {
		$this->sesionAnio = $sesionAnio;

		return $this;
	}

	/**
	 * Get sesionAnio
	 *
	 * @return integer
	 */
	public function getSesionAnio() {
		return $this->sesionAnio;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return Expediente
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
	 * @return Expediente
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set tipoExpediente
	 *
	 * @param \App\Entity\TipoExpediente $tipoExpediente
	 *
	 * @return Expediente
	 */
	public function setTipoExpediente( \App\Entity\TipoExpediente $tipoExpediente = null ) {
		$this->tipoExpediente = $tipoExpediente;

		return $this;
	}

	/**
	 * Get tipoExpediente
	 *
	 * @return \App\Entity\TipoExpediente
	 */
	public function getTipoExpediente() {
		return $this->tipoExpediente;
	}

	/**
	 * Add iniciadore
	 *
	 * @param \App\Entity\IniciadorExpediente $iniciadore
	 *
	 * @return Expediente
	 */
	public function addIniciadore( \App\Entity\IniciadorExpediente $iniciadore ) {

		$iniciadore->setExpediente( $this );

		$this->iniciadores->add( $iniciadore );

		return $this;
	}

	/**
	 * Remove iniciadore
	 *
	 * @param \App\Entity\IniciadorExpediente $iniciadore
	 */
	public function removeIniciadore( \App\Entity\IniciadorExpediente $iniciadore ) {
		$this->iniciadores->removeElement( $iniciadore );
	}

	/**
	 * Get iniciadores
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getIniciadores() {
		return $this->iniciadores;
	}

	/**
	 * Set iniciadorParticular
	 *
	 * @param \App\Entity\Persona $iniciadorParticular
	 *
	 * @return Expediente
	 */
	public function setIniciadorParticular( \App\Entity\Persona $iniciadorParticular = null ) {
		$this->iniciadorParticular = $iniciadorParticular;

		return $this;
	}

	/**
	 * Get iniciadorParticular
	 *
	 * @return \App\Entity\Persona
	 */
	public function getIniciadorParticular() {
		return $this->iniciadorParticular;
	}

	/**
	 * Set dependencia
	 *
	 * @param \App\Entity\Dependencia $dependencia
	 *
	 * @return Expediente
	 */
	public function setDependencia( \App\Entity\Dependencia $dependencia = null ) {
		$this->dependencia = $dependencia;

		return $this;
	}

	/**
	 * Get dependencia
	 *
	 * @return \App\Entity\Dependencia
	 */
	public function getDependencia() {
		return $this->dependencia;
	}

	/**
	 * Add giroAdministrativo
	 *
	 * @param \App\Entity\GiroAdministrativo $giroAdministrativo
	 *
	 * @return Expediente
	 */
	public function addGiroAdministrativo( \App\Entity\GiroAdministrativo $giroAdministrativo ) {

		$giroAdministrativo->setExpediente( $this );

		$this->giroAdministrativos->add( $giroAdministrativo );

		return $this;
	}

	/**
	 * Remove giroAdministrativo
	 *
	 * @param \App\Entity\GiroAdministrativo $giroAdministrativo
	 */
	public function removeGiroAdministrativo( \App\Entity\GiroAdministrativo $giroAdministrativo ) {
		$this->giroAdministrativos->removeElement( $giroAdministrativo );
	}

	/**
	 * Get giroAdministrativos
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGiroAdministrativos() {
		return $this->giroAdministrativos;
	}

	/**
	 * Add giro
	 *
	 * @param \App\Entity\Giro $giro
	 *
	 * @return Expediente
	 */
	public function addGiro( \App\Entity\Giro $giro ) {
//		$this->giros[] = $giro;
//
//		return $this;

		$giro->setExpediente( $this );

		$this->giros->add( $giro );

		return $this;
	}

	/**
	 * Remove giro
	 *
	 * @param \App\Entity\Giro $giro
	 */
	public function removeGiro( \App\Entity\Giro $giro ) {
		$this->giros->removeElement( $giro );
	}

	/**
	 * Get giros
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGiros() {
		return $this->giros;
	}

	public function getGirosOrdenados( Sesion $sesion = null ) {
		$giros = new ArrayCollection();
		if ( ! $sesion ) {
			$giros = $this->getGiros();
		} else {
			/** @var BoletinAsuntoEntrado $bae */
			$bae         = $sesion->getBae()->first();
			$proyectoBAE = $bae->getProyectos()->filter( function ( ProyectoBAE $proyectoBAE ) {
				return $proyectoBAE->getExpediente()->getId() == $this->getId();
			} );
			if ( $proyectoBAE->count() ) {
				/** @var ProyectoBAE $proyectoBAE */
				$proyectoBAE = $proyectoBAE->first();
				$giros       = $proyectoBAE->getGiros();
			}
		}

		$iterator = $giros->getIterator();

		$iterator->uasort( function ( Giro $a, Giro $b ) {
			if ( $a->getCabecera() ) {
				return - 1;
			} elseif ( $b->getCabecera() ) {
				return 1;
			} else {
				return ( $a->getOrden() < $b->getOrden() ) ? - 1 : 1;
			}
		} );

		return new ArrayCollection( iterator_to_array( $iterator ) );
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return Expediente
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
	 * @return Expediente
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Set numeroNota
	 *
	 * @param string $numeroNota
	 *
	 * @return Expediente
	 */
	public function setNumeroNota( $numeroNota ) {
		$this->numeroNota = $numeroNota;

		return $this;
	}

	/**
	 * Get numeroNota
	 *
	 * @return string
	 */
	public function getNumeroNota() {
		return $this->numeroNota;
	}

	/**
	 * Set tipoProyecto
	 *
	 * @param \App\Entity\TipoProyecto $tipoProyecto
	 *
	 * @return Expediente
	 */
	public function setTipoProyecto( \App\Entity\TipoProyecto $tipoProyecto = null ) {
		$this->tipoProyecto = $tipoProyecto;

		return $this;
	}

	/**
	 * Get tipoProyecto
	 *
	 * @return \App\Entity\TipoProyecto
	 */
	public function getTipoProyecto() {
		return $this->tipoProyecto;
	}

	/**
	 * Set texto
	 *
	 * @param string $texto
	 *
	 * @return Expediente
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
	 * Set borrador
	 *
	 * @param boolean $borrador
	 *
	 * @return Expediente
	 */
	public function setBorrador( $borrador ) {
		$this->borrador = $borrador;

		return $this;
	}

	/**
	 * Get borrador
	 *
	 * @return boolean
	 */
	public function getBorrador() {
		return $this->borrador;
	}

	/**
	 * Set periodoLegislativo
	 *
	 * @param \App\Entity\PeriodoLegislativo $periodoLegislativo
	 *
	 * @return Expediente
	 */
	public function setPeriodoLegislativo( \App\Entity\PeriodoLegislativo $periodoLegislativo = null ) {
		$this->periodoLegislativo = $periodoLegislativo;

		return $this;
	}

	/**
	 * Get periodoLegislativo
	 *
	 * @return \App\Entity\PeriodoLegislativo
	 */
	public function getPeriodoLegislativo() {
		return $this->periodoLegislativo;
	}

	/**
	 * Set codigoReferencia
	 *
	 * @param string $codigoReferencia
	 *
	 * @return Expediente
	 */
	public function setCodigoReferencia( $codigoReferencia ) {
		$this->codigoReferencia = $codigoReferencia;

		return $this;
	}

	/**
	 * Get codigoReferencia
	 *
	 * @return string
	 */
	public function getCodigoReferencia() {
		return $this->codigoReferencia;
	}

	/**
	 * Set numeroDeHojas
	 *
	 * @param string $numeroDeHojas
	 *
	 * @return Expediente
	 */
	public function setNumeroDeHojas( $numeroDeHojas ) {
		$this->numeroDeHojas = $numeroDeHojas;

		return $this;
	}

	/**
	 * Get numeroDeHojas
	 *
	 * @return string
	 */
	public function getNumeroDeHojas() {
		return $this->numeroDeHojas;
	}

	/**
	 * Set extractoTemario
	 *
	 * @param string $extractoTemario
	 *
	 * @return Expediente
	 */
	public function setExtractoTemario( $extractoTemario ) {
		$this->extractoTemario = $extractoTemario;

		return $this;
	}

	/**
	 * Get extractoTemario
	 *
	 * @return string
	 */
	public function getExtractoTemario() {
		return $this->extractoTemario;
	}

	/**
	 * Set extractoDictamen
	 *
	 * @param string $extractoDictamen
	 *
	 * @return Expediente
	 */
	public function setExtractoDictamen( $extractoDictamen ) {
		$this->extractoDictamen = $extractoDictamen;

		return $this;
	}

	/**
	 * Get extractoDictamen
	 *
	 * @return string
	 */
	public function getExtractoDictamen() {
		return $this->extractoDictamen;
	}

	/**
	 * Add anexo
	 *
	 * @param \App\Entity\AnexoExpediente $anexo
	 *
	 * @return Expediente
	 */
	public function addAnexo( \App\Entity\AnexoExpediente $anexo ) {

		$anexo->setExpediente( $this );

		$this->anexos->add( $anexo );

		return $this;
	}

	/**
	 * Remove anexo
	 *
	 * @param \App\Entity\AnexoExpediente $anexo
	 */
	public function removeAnexo( \App\Entity\AnexoExpediente $anexo ) {
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
	 * Set fechaPresentacion
	 *
	 * @param \DateTime $fechaPresentacion
	 *
	 * @return Expediente
	 */
	public function setFechaPresentacion( $fechaPresentacion ) {
		$this->fechaPresentacion = $fechaPresentacion;

		return $this;
	}

	/**
	 * Get fechaPresentacion
	 *
	 * @return \DateTime
	 */
	public function getFechaPresentacion() {
		return $this->fechaPresentacion;
	}

	/**
	 * @return bool
	 */
	public function esProyectoDeConcejal() {
		return $this->getIniciadores()->exists( function ( $i, IniciadorExpediente $ie ) {
			return $ie->getAutor()
			       && ( $ie->getIniciador()->getCargoPersona()->getCargo()->getId() == $_ENV['CARGO_CONCEJAL'] ||
			            $ie->getIniciador()->getCargoPersona()->getCargo()->getId() == $_ENV['CARGO_PRESIDENTE'] );
		} );
	}

	/**
	 * @return bool
	 */
	public function esProyectoDeDEM() {
//		return $this->getLetra() == 'M' && (strpos($this->getExpediente(), 'RM') === 0);
		return (strpos($this->getExpediente(), 'RM') === 0) || $this->getProyectoDem();
//		return $this->getIniciadores()->exists( function ( $i, IniciadorExpediente $ie ) {
//			return $ie->getAutor()
//			       && $ie->getIniciador()->getCargoPersona()->getAreaAdministrativa()
//			       && $ie->getIniciador()->getCargoPersona()->getAreaAdministrativa()->getId() == AreaAdministrativa::AREA_ADMINISTRATIVA_DEM;
//		} );
	}

	/**
	 * @return bool
	 */
	public function esProyectoDeDefensor() {
		return $this->getIniciadores()->exists( function ( $i, IniciadorExpediente $ie ) {
			return $ie->getAutor()
			       && $ie->getIniciador()->getCargoPersona()->getCargo()->getId() == $_ENV['CARGO_DEFENSOR'];
		} );
	}

	/**
	 * Add dictamene
	 *
	 * @param \App\Entity\Dictamen $dictamene
	 *
	 * @return Expediente
	 */
	public function addDictamene( \App\Entity\Dictamen $dictamene ) {
		$this->dictamenes[] = $dictamene;

		return $this;
	}

	/**
	 * Remove dictamene
	 *
	 * @param \App\Entity\Dictamen $dictamene
	 */
	public function removeDictamene( \App\Entity\Dictamen $dictamene ) {
		$this->dictamenes->removeElement( $dictamene );
	}

	/**
	 * Get dictamenes
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getDictamenes() {
		return $this->dictamenes;
	}

	/**
	 * Set nota
	 *
	 * @param boolean $nota
	 *
	 * @return Expediente
	 */
	public function setNota( $nota ) {
		$this->nota = $nota;

		return $this;
	}

	/**
	 * Get nota
	 *
	 * @return boolean
	 */
	public function getNota() {
		return $this->nota;
	}

	/**
	 * Set asignadoPor
	 *
	 * @param \App\Entity\Usuario $asignadoPor
	 *
	 * @return Expediente
	 */
	public function setAsignadoPor( \App\Entity\Usuario $asignadoPor = null ) {
		$this->asignadoPor = $asignadoPor;

		return $this;
	}

	/**
	 * Get asignadoPor
	 *
	 * @return \App\Entity\Usuario
	 */
	public function getAsignadoPor() {
		return $this->asignadoPor;
	}

	/**
	 * @param Sesion|null $sesion
	 *
	 * @return string
	 */
	public function getTextoDelGiro( Sesion $sesion = null ) {
		$giros = $this->getGirosOrdenados( $sesion )->filter( function ( Giro $giro ) {
			return $giro->getComisionDestino() != null;
		} )->map( function ( Giro $giro ) {
			return '<strong title="' . $giro->getComisionDestino()->getNombre() . '">' . $giro->getComisionDestino()->getAbreviacion() . '</strong>';
		} );

		if ( count( $giros ) > 1 ) {
			$textoDelGiro = 'A las Comisiones de ';
		} else {
			$textoDelGiro = 'A la Comisión de ';
		}


		if ( count( $giros ) == 1 ) {
			$textoDelGiro .= $giros[0];
		} else {
			$count = count( $giros );
			$pos   = 0;
			foreach ( $giros as $i => $giro ) {
				if ( $pos == $count - 1 ) {
					$textoDelGiro .= ' y de ';
				} elseif ( $pos != 0 ) {
					$textoDelGiro .= '; ';
				}

				$textoDelGiro .= $giro;
				$pos ++;
			}
		}

		return $textoDelGiro;
	}

    /**
     * Add expedientesAdjunto
     *
     * @param \App\Entity\ExpedienteAdjunto $expedientesAdjunto
     *
     * @return Expediente
     */
    public function addExpedientesAdjunto(\App\Entity\ExpedienteAdjunto $expedientesAdjunto)
    {
	    $expedientesAdjunto->setExpediente( $this );

	    $this->expedientesAdjunto->add( $expedientesAdjunto );

	    return $this;
    }

    /**
     * Remove expedientesAdjunto
     *
     * @param \App\Entity\ExpedienteAdjunto $expedientesAdjunto
     */
    public function removeExpedientesAdjunto(\App\Entity\ExpedienteAdjunto $expedientesAdjunto)
    {
        $this->expedientesAdjunto->removeElement($expedientesAdjunto);
    }

    /**
     * Get expedientesAdjunto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExpedientesAdjunto()
    {
        return $this->expedientesAdjunto;
    }

    /**
     * Set proyectoDem
     *
     * @param boolean $proyectoDem
     *
     * @return Expediente
     */
    public function setProyectoDem($proyectoDem)
    {
        $this->proyectoDem = $proyectoDem;

        return $this;
    }

    /**
     * Get proyectoDem
     *
     * @return boolean
     */
    public function getProyectoDem()
    {
        return $this->proyectoDem;
    }
}
