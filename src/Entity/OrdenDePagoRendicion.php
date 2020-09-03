<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Base\BaseClass;

/**
 * OrdenDePagoRendicion
 *
 * @ORM\Table(name="orden_de_pago_rendicion")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\OrdenDePagoRendicionRepository")
 */
class OrdenDePagoRendicion extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha", type="date")
	 */
	private $fecha;

	/**
	 * @var $ordenDePago
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\OrdenDePago", inversedBy="fechaRendicion")
	 * @ORM\JoinColumn(name="orden_de_pago_id", referencedColumnName="id", nullable=true)
	 */
	private $ordenDePago;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $ordenDePagoSrc;

	/**
	 * @Vich\UploadableField(mapping="orden_de_pago", fileNameProperty="ordenDePagoSrc")
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
	 * @return OrdenDePagoRendicion
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
	 * Set fecha
	 *
	 * @param \DateTime $fecha
	 *
	 * @return OrdenDePagoRendicion
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
	 * Set ordenDePago
	 *
	 * @param \App\Entity\OrdenDePago $ordenDePago
	 *
	 * @return OrdenDePagoRendicion
	 */
	public function setOrdenDePago( \App\Entity\OrdenDePago $ordenDePago = null ) {
		$this->ordenDePago = $ordenDePago;

		return $this;
	}

	/**
	 * Get ordenDePago
	 *
	 * @return \App\Entity\OrdenDePago
	 */
	public function getOrdenDePago() {
		return $this->ordenDePago;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return OrdenDePagoRendicion
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
	 * @return OrdenDePagoRendicion
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return OrdenDePagoRendicion
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
	 * @return OrdenDePagoRendicion
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getOrdenDePagoSrc() {
		return $this->ordenDePagoSrc;
	}

	/**
	 * @param string $ordenDePagoSrc
	 */
	public function setOrdenDePagoSrc( $ordenDePagoSrc ) {
		$this->ordenDePagoSrc = $ordenDePagoSrc;
	}
}
