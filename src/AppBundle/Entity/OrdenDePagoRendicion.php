<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * OrdenDePagoRendicion
 *
 * @ORM\Table(name="orden_de_pago_rendicion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenDePagoRendicionRepository")
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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OrdenDePago", inversedBy="fechaRendicion")
	 * @ORM\JoinColumn(name="orden_de_pago_id", referencedColumnName="id", nullable=true)
	 */
	private $ordenDePago;

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
	 * @param \AppBundle\Entity\OrdenDePago $ordenDePago
	 *
	 * @return OrdenDePagoRendicion
	 */
	public function setOrdenDePago( \AppBundle\Entity\OrdenDePago $ordenDePago = null ) {
		$this->ordenDePago = $ordenDePago;

		return $this;
	}

	/**
	 * Get ordenDePago
	 *
	 * @return \AppBundle\Entity\OrdenDePago
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
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return OrdenDePagoRendicion
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
	 * @return OrdenDePagoRendicion
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}
}
