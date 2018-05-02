<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Sesion
 *
 * @ORM\Table(name="sesion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SesionRepository")
 */
class Sesion extends BaseClass {
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
	 * @ORM\Column(name="titulo", type="string", length=255)
	 */
	private $titulo;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha", type="date")
	 */
	private $fecha;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="orden_del_dia", type="text", nullable=true)
	 */
	private $ordenDelDia;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="asuntos_entrados", type="text", nullable=true)
	 */
	private $asuntosEntrados;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="acta", type="text", nullable=true)
	 */
	private $acta;

	/**
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mocion", mappedBy="sesion", cascade={"persist", "remove"})
	 */
	private $mociones;

	/**
	 * @var Collection|OrdenDelDia[]
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrdenDelDia", mappedBy="sesion", cascade={"persist", "remove"})
	 */
	private $od;

	/**
	 * @var Collection|BoletinAsuntoEntrado[]
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\BoletinAsuntoEntrado", mappedBy="sesion", cascade={"persist", "remove"})
	 */
	private $bae;

	/**
	 * @var $tipoSesion
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Parametro")
	 * @ORM\JoinColumn(name="tipo_sesion_id", referencedColumnName="id", nullable=true)
	 */
	private $tipoSesion;

	/**
	 * @var LogExpediente[] $logs
	 *
	 * @ORM\OneToMany(targetEntity="MesaEntradaBundle\Entity\LogExpediente", mappedBy="sesion", cascade={"persist"})
	 * @ORM\OrderBy({"id" = "DESC"})
	 */
	private $logs;

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getTitulo();
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
	 * Set titulo
	 *
	 * @param string $titulo
	 *
	 * @return Sesion
	 */
	public function setTitulo( $titulo ) {
		$this->titulo = $titulo;

		return $this;
	}

	/**
	 * Get titulo
	 *
	 * @return string
	 */
	public function getTitulo() {
		return $this->titulo;
	}

	/**
	 * Set fecha
	 *
	 * @param \DateTime $fecha
	 *
	 * @return Sesion
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
	 * Set ordenDelDia
	 *
	 * @param string $ordenDelDia
	 *
	 * @return Sesion
	 */
	public function setOrdenDelDia( $ordenDelDia ) {
		$this->ordenDelDia = $ordenDelDia;

		return $this;
	}

	/**
	 * Get ordenDelDia
	 *
	 * @return string
	 */
	public function getOrdenDelDia() {
		return $this->ordenDelDia;
	}

	/**
	 * Set asuntosEntrados
	 *
	 * @param string $asuntosEntrados
	 *
	 * @return Sesion
	 */
	public function setAsuntosEntrados( $asuntosEntrados ) {
		$this->asuntosEntrados = $asuntosEntrados;

		return $this;
	}

	/**
	 * Get asuntosEntrados
	 *
	 * @return string
	 */
	public function getAsuntosEntrados() {
		return $this->asuntosEntrados;
	}

	/**
	 * Set acta
	 *
	 * @param string $acta
	 *
	 * @return Sesion
	 */
	public function setActa( $acta ) {
		$this->acta = $acta;

		return $this;
	}

	/**
	 * Get acta
	 *
	 * @return string
	 */
	public function getActa() {
		return $this->acta;
	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return Sesion
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
	 * @return Sesion
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
	 * @return Sesion
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
	 * @return Sesion
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
		$this->actualizadoPor = $actualizadoPor;

		return $this;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->mociones = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Add mocione
	 *
	 * @param \AppBundle\Entity\Mocion $mocione
	 *
	 * @return Sesion
	 */
	public function addMocione( \AppBundle\Entity\Mocion $mocione ) {
		$this->mociones[] = $mocione;

		return $this;
	}

	/**
	 * Remove mocione
	 *
	 * @param \AppBundle\Entity\Mocion $mocione
	 */
	public function removeMocione( \AppBundle\Entity\Mocion $mocione ) {
		$this->mociones->removeElement( $mocione );
	}

	/**
	 * Get mociones
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getMociones() {
		return $this->mociones;
	}

	/**
	 * Set tipoSesion
	 *
	 * @param \AppBundle\Entity\Parametro $tipoSesion
	 *
	 * @return Sesion
	 */
	public function setTipoSesion( \AppBundle\Entity\Parametro $tipoSesion = null ) {
		$this->tipoSesion = $tipoSesion;

		return $this;
	}

	/**
	 * Get tipoSesion
	 *
	 * @return \AppBundle\Entity\Parametro
	 */
	public function getTipoSesion() {
		return $this->tipoSesion;
	}

    /**
     * Add od
     *
     * @param \AppBundle\Entity\OrdenDelDia $od
     *
     * @return Sesion
     */
    public function addOd(\AppBundle\Entity\OrdenDelDia $od)
    {
        $this->od[] = $od;

        return $this;
    }

    /**
     * Remove od
     *
     * @param \AppBundle\Entity\OrdenDelDia $od
     */
    public function removeOd(\AppBundle\Entity\OrdenDelDia $od)
    {
        $this->od->removeElement($od);
    }

    /**
     * Get od
     *
     * @return Collection|OrdenDelDia[]
     */
    public function getOd()
    {
        return $this->od;
    }

    /**
     * Add bae
     *
     * @param \AppBundle\Entity\BoletinAsuntoEntrado $bae
     *
     * @return Sesion
     */
    public function addBae(\AppBundle\Entity\BoletinAsuntoEntrado $bae)
    {
        $this->bae[] = $bae;

        return $this;
    }

    /**
     * Remove bae
     *
     * @param \AppBundle\Entity\BoletinAsuntoEntrado $bae
     */
    public function removeBae(\AppBundle\Entity\BoletinAsuntoEntrado $bae)
    {
        $this->bae->removeElement($bae);
    }

    /**
     * Get bae
     *
     * @return \Doctrine\Common\Collections\Collection|BoletinAsuntoEntrado[]
     */
    public function getBae()
    {
        return $this->bae;
    }

    /**
     * Add log
     *
     * @param \MesaEntradaBundle\Entity\LogExpediente $log
     *
     * @return Sesion
     */
    public function addLog(\MesaEntradaBundle\Entity\LogExpediente $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \MesaEntradaBundle\Entity\LogExpediente $log
     */
    public function removeLog(\MesaEntradaBundle\Entity\LogExpediente $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
