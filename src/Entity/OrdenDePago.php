<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Base\BaseClass;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrdenDePago
 *
 * @ORM\Table(name="orden_de_pago")
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"numero", "periodoLegislativo"},
 *     errorPath="numero",
 *     message="Ya existe la orden de pago en el año"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OrdenDePagoRepository")
 */
class OrdenDePago extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="numero", type="integer", nullable=true)
	 */
	private $numero;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="folios", type="string", length=255, nullable=true)
	 */
	private $folios;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_estante", type="string", length=255, nullable=true)
	 */
	private $numeroEstante;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_caja", type="string", length=255, nullable=true)
	 */
	private $numeroCaja;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="pagina_inicio", type="string", length=255, nullable=true)
	 */
	private $paginaInicio;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="pagina_fin", type="string", length=255, nullable=true)
	 */
	private $paginaFin;

	/**
	 * @var $tipoOrdenPago
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\TipoOrdenPago")
	 * @ORM\JoinColumn(name="tipo_orden_pago_id", referencedColumnName="id", nullable=false)
	 */
	private $tipoOrdenPago;

	/**
	 * @var $decreto
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Decreto")
	 * @ORM\JoinColumn(name="decreto_id", referencedColumnName="id", nullable=true)
	 */
	private $decreto;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\OrdenDePagoRendicion", mappedBy="ordenDePago", cascade={"persist", "remove"})
	 */
	private $fechaRendicion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="observacion", type="text", nullable=true)
	 */
	private $observacion;

	/**
	 * @var $periodoLegislativo
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\PeriodoLegislativo")
	 * @ORM\JoinColumn(name="periodo_legislativo_id", referencedColumnName="id", nullable=true)
	 */
	private $periodoLegislativo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $ordenDePago;


	/**
	 * @Vich\UploadableField(mapping="orden_de_pago", fileNameProperty="ordenDePago")
	 * @var File
	 */
	private $ordenDePagoFile;

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return OrdenDePago
	 */
	public function setOrdenDePagoFile( File $file = null ) {
		$this->ordenDePagoFile = $file;

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
	public function getOrdenDePagoFile() {
		return $this->ordenDePagoFile;
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
	 * Set numero
	 *
	 * @param integer $numero
	 *
	 * @return OrdenDePago
	 */
	public function setNumero( $numero ) {
		$this->numero = $numero;

		return $this;
	}

	/**
	 * Get numero
	 *
	 * @return int
	 */
	public function getNumero() {
		return $this->numero;
	}

	/**
	 * Set folios
	 *
	 * @param string $folios
	 *
	 * @return OrdenDePago
	 */
	public function setFolios( $folios ) {
		$this->folios = $folios;

		return $this;
	}

	/**
	 * Get folios
	 *
	 * @return string
	 */
	public function getFolios() {
		return $this->folios;
	}

	/**
	 * Set paginaInicio
	 *
	 * @param string $paginaInicio
	 *
	 * @return OrdenDePago
	 */
	public function setPaginaInicio( $paginaInicio ) {
		$this->paginaInicio = $paginaInicio;

		return $this;
	}

	/**
	 * Get paginaInicio
	 *
	 * @return string
	 */
	public function getPaginaInicio() {
		return $this->paginaInicio;
	}

	/**
	 * Set paginaFin
	 *
	 * @param string $paginaFin
	 *
	 * @return OrdenDePago
	 */
	public function setPaginaFin( $paginaFin ) {
		$this->paginaFin = $paginaFin;

		return $this;
	}

	/**
	 * Get paginaFin
	 *
	 * @return string
	 */
	public function getPaginaFin() {
		return $this->paginaFin;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return OrdenDePago
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
	 * @return OrdenDePago
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set tipoOrdenPago
	 *
	 * @param \App\Entity\TipoOrdenPago $tipoOrdenPago
	 *
	 * @return OrdenDePago
	 */
	public function setTipoOrdenPago( \App\Entity\TipoOrdenPago $tipoOrdenPago = null ) {
		$this->tipoOrdenPago = $tipoOrdenPago;

		return $this;
	}

	/**
	 * Get tipoOrdenPago
	 *
	 * @return \App\Entity\TipoOrdenPago
	 */
	public function getTipoOrdenPago() {
		return $this->tipoOrdenPago;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return OrdenDePago
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
	 * @return OrdenDePago
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Set decreto
	 *
	 * @param \App\Entity\Decreto $decreto
	 *
	 * @return OrdenDePago
	 */
	public function setDecreto( \App\Entity\Decreto $decreto = null ) {
		$this->decreto = $decreto;

		return $this;
	}

	/**
	 * Get decreto
	 *
	 * @return \App\Entity\Decreto
	 */
	public function getDecreto() {
		return $this->decreto;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->fechaRendicion = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Add fechaRendicion
	 *
	 * @param \App\Entity\OrdenDePagoRendicion $fechaRendicion
	 *
	 * @return OrdenDePago
	 */
	public function addFechaRendicion( \App\Entity\OrdenDePagoRendicion $fechaRendicion ) {

		$fechaRendicion->setOrdenDePago( $this );

		$this->fechaRendicion->add( $fechaRendicion );

		return $this;
	}

	/**
	 * Remove fechaRendicion
	 *
	 * @param \App\Entity\OrdenDePagoRendicion $fechaRendicion
	 */
	public function removeFechaRendicion( \App\Entity\OrdenDePagoRendicion $fechaRendicion ) {
		$this->fechaRendicion->removeElement( $fechaRendicion );
	}

	/**
	 * Get fechaRendicion
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getFechaRendicion() {
		return $this->fechaRendicion;
	}

	/**
	 * Set numeroEstante
	 *
	 * @param string $numeroEstante
	 *
	 * @return OrdenDePago
	 */
	public function setNumeroEstante( $numeroEstante ) {
		$this->numeroEstante = $numeroEstante;

		return $this;
	}

	/**
	 * Get numeroEstante
	 *
	 * @return string
	 */
	public function getNumeroEstante() {
		return $this->numeroEstante;
	}

	/**
	 * Set numeroCaja
	 *
	 * @param string $numeroCaja
	 *
	 * @return OrdenDePago
	 */
	public function setNumeroCaja( $numeroCaja ) {
		$this->numeroCaja = $numeroCaja;

		return $this;
	}

	/**
	 * Get numeroCaja
	 *
	 * @return string
	 */
	public function getNumeroCaja() {
		return $this->numeroCaja;
	}

	/**
	 * Set observacion
	 *
	 * @param string $observacion
	 *
	 * @return OrdenDePago
	 */
	public function setObservacion( $observacion ) {
		$this->observacion = $observacion;

		return $this;
	}

	/**
	 * Get observacion
	 *
	 * @return string
	 */
	public function getObservacion() {
		return $this->observacion;
	}

	/**
	 * Set ordenDePago
	 *
	 * @param string $ordenDePago
	 *
	 * @return OrdenDePago
	 */
	public function setOrdenDePago( $ordenDePago ) {
		$this->ordenDePago = $ordenDePago;

		return $this;
	}

	/**
	 * Get ordenDePago
	 *
	 * @return string
	 */
	public function getOrdenDePago() {
		return $this->ordenDePago;
	}

    /**
     * Set periodoLegislativo
     *
     * @param \App\Entity\PeriodoLegislativo $periodoLegislativo
     *
     * @return OrdenDePago
     */
    public function setPeriodoLegislativo(\App\Entity\PeriodoLegislativo $periodoLegislativo = null)
    {
        $this->periodoLegislativo = $periodoLegislativo;

        return $this;
    }

    /**
     * Get periodoLegislativo
     *
     * @return \App\Entity\PeriodoLegislativo
     */
    public function getPeriodoLegislativo()
    {
        return $this->periodoLegislativo;
    }
}
