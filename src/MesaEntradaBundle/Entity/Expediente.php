<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use UtilBundle\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Expediente
 *
 * @ORM\Table(name="expediente")
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"expediente", "anio", "letra"},
 *     errorPath="expediente",
 *     message="Ya existe el expediente en el año y con esta letra"
 * )
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\ExpedienteRepository")
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
	 * @ORM\Column(name="expediente", type="string", length=255)
	 */
	private $expediente;

	/**
	 * @ORM\Column(name="anio",type="string", length=255)
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
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\TipoExpediente")
	 * @ORM\JoinColumn(name="tipo_expediente_id", referencedColumnName="id")
	 */
	private $tipoExpediente;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Iniciador")
	 * @ORM\JoinColumn(name="iniciador_id", referencedColumnName="id")
	 */
	private $iniciador;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Persona")
	 * @ORM\JoinColumn(name="iniciador_particular_id", referencedColumnName="id")
	 */
	private $iniciadorParticular;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dependencia")
	 * @ORM\JoinColumn(name="dependencia_id", referencedColumnName="id")
	 */
	private $dependencia;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="MesaEntradaBundle\Entity\GiroAdministrativo", mappedBy="expediente", cascade={"persist"})
	 *
	 */
	private $giroAdministrativos;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="MesaEntradaBundle\Entity\Giro", mappedBy="expediente", cascade={"persist"})
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
//			$this->updatedAt = new \DateTimeImmutable();
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

	public function __toString() {
		return $this->letra;
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
	 * @param \MesaEntradaBundle\Entity\TipoExpediente $tipoExpediente
	 *
	 * @return Expediente
	 */
	public function setTipoExpediente( \MesaEntradaBundle\Entity\TipoExpediente $tipoExpediente = null ) {
		$this->tipoExpediente = $tipoExpediente;

		return $this;
	}

	/**
	 * Get tipoExpediente
	 *
	 * @return \MesaEntradaBundle\Entity\TipoExpediente
	 */
	public function getTipoExpediente() {
		return $this->tipoExpediente;
	}

	/**
	 * Set iniciador
	 *
	 * @param \MesaEntradaBundle\Entity\Iniciador $iniciador
	 *
	 * @return Expediente
	 */
	public function setIniciador( \MesaEntradaBundle\Entity\Iniciador $iniciador = null ) {
		$this->iniciador = $iniciador;

		return $this;
	}

	/**
	 * Get iniciador
	 *
	 * @return \MesaEntradaBundle\Entity\Iniciador
	 */
	public function getIniciador() {
		return $this->iniciador;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return Expediente
	 */
	public function setCreadoPor( \UsuariosBundle\Entity\Usuario $creadoPor = null ) {
		$this->creadoPor = $creadoPor;

		return $this;
	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $actualizadoPor
	 *
	 * @return Expediente
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Set iniciadorParticular
	 *
	 * @param \AppBundle\Entity\Persona $iniciadorParticular
	 *
	 * @return Expediente
	 */
	public function setIniciadorParticular( \AppBundle\Entity\Persona $iniciadorParticular = null ) {
		$this->iniciadorParticular = $iniciadorParticular;

		return $this;
	}

	/**
	 * Get iniciadorParticular
	 *
	 * @return \AppBundle\Entity\Persona
	 */
	public function getIniciadorParticular() {
		return $this->iniciadorParticular;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->giros               = new \Doctrine\Common\Collections\ArrayCollection();
		$this->giroAdministrativos = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Add giro
	 *
	 * @param \MesaEntradaBundle\Entity\Giro $giro
	 *
	 *
	 * @return Expediente
	 */
	public function addGiro( \MesaEntradaBundle\Entity\Giro $giro ) {

		$giro->setExpediente( $this );

		$this->giros->add( $giro );

		return $this;
	}

	/**
	 * Remove giro
	 *
	 * @param \MesaEntradaBundle\Entity\Giro $giro
	 */
	public function removeGiro( \MesaEntradaBundle\Entity\Giro $giro ) {
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

	/**
	 * Add giroAdministrativo
	 *
	 * @param \MesaEntradaBundle\Entity\GiroAdministrativo $giroAdministrativo
	 *
	 * @return Expediente
	 */
	public function addGiroAdministrativo( \MesaEntradaBundle\Entity\GiroAdministrativo $giroAdministrativo ) {

		$giroAdministrativo->setExpediente( $this );

		$this->giroAdministrativos->add( $giroAdministrativo );

		return $this;
	}

	/**
	 * Remove giroAdministrativo
	 *
	 * @param \MesaEntradaBundle\Entity\GiroAdministrativo $giroAdministrativo
	 */
	public function removeGiroAdministrativo( \MesaEntradaBundle\Entity\GiroAdministrativo $giroAdministrativo ) {
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
     * Set dependencia
     *
     * @param \AppBundle\Entity\Dependencia $dependencia
     *
     * @return Expediente
     */
    public function setDependencia(\AppBundle\Entity\Dependencia $dependencia = null)
    {
        $this->dependencia = $dependencia;

        return $this;
    }

    /**
     * Get dependencia
     *
     * @return \AppBundle\Entity\Dependencia
     */
    public function getDependencia()
    {
        return $this->dependencia;
    }

    /**
     * Set sesionNumero
     *
     * @param integer $sesionNumero
     *
     * @return Expediente
     */
    public function setSesionNumero($sesionNumero)
    {
        $this->sesionNumero = $sesionNumero;

        return $this;
    }

    /**
     * Get sesionNumero
     *
     * @return integer
     */
    public function getSesionNumero()
    {
        return $this->sesionNumero;
    }

    /**
     * Set sesionAnio
     *
     * @param integer $sesionAnio
     *
     * @return Expediente
     */
    public function setSesionAnio($sesionAnio)
    {
        $this->sesionAnio = $sesionAnio;

        return $this;
    }

    /**
     * Get sesionAnio
     *
     * @return integer
     */
    public function getSesionAnio()
    {
        return $this->sesionAnio;
    }
}
