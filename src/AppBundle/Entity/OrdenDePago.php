<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * OrdenDePago
 *
 * @ORM\Table(name="orden_de_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenDePagoRepository")
 */
class OrdenDePago extends BaseClass
{
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_rendicion", type="date", nullable=true)
     */
    private $fechaRendicion;

    /**
     * @var string
     *
     * @ORM\Column(name="folios", type="string", length=255, nullable=true)
     */
    private $folios;

    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="string", length=255, nullable=true)
     */
    private $ubicacion;

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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoOrdenPago")
	 * @ORM\JoinColumn(name="tipo_orden_pago_id", referencedColumnName="id", nullable=true)
	 */
	private $tipoOrdenPago;

	/**
	 * @var $decreto
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Decreto")
	 * @ORM\JoinColumn(name="decreto_id", referencedColumnName="id", nullable=true)
	 */
	private $decreto;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return OrdenDePago
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fechaRendicion
     *
     * @param \DateTime $fechaRendicion
     *
     * @return OrdenDePago
     */
    public function setFechaRendicion($fechaRendicion)
    {
        $this->fechaRendicion = $fechaRendicion;

        return $this;
    }

    /**
     * Get fechaRendicion
     *
     * @return \DateTime
     */
    public function getFechaRendicion()
    {
        return $this->fechaRendicion;
    }

    /**
     * Set folios
     *
     * @param string $folios
     *
     * @return OrdenDePago
     */
    public function setFolios($folios)
    {
        $this->folios = $folios;

        return $this;
    }

    /**
     * Get folios
     *
     * @return string
     */
    public function getFolios()
    {
        return $this->folios;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     *
     * @return OrdenDePago
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set paginaInicio
     *
     * @param string $paginaInicio
     *
     * @return OrdenDePago
     */
    public function setPaginaInicio($paginaInicio)
    {
        $this->paginaInicio = $paginaInicio;

        return $this;
    }

    /**
     * Get paginaInicio
     *
     * @return string
     */
    public function getPaginaInicio()
    {
        return $this->paginaInicio;
    }

    /**
     * Set paginaFin
     *
     * @param string $paginaFin
     *
     * @return OrdenDePago
     */
    public function setPaginaFin($paginaFin)
    {
        $this->paginaFin = $paginaFin;

        return $this;
    }

    /**
     * Get paginaFin
     *
     * @return string
     */
    public function getPaginaFin()
    {
        return $this->paginaFin;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return OrdenDePago
     */
    public function setFechaCreacion($fechaCreacion)
    {
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
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set tipoOrdenPago
     *
     * @param \AppBundle\Entity\TipoOrdenPago $tipoOrdenPago
     *
     * @return OrdenDePago
     */
    public function setTipoOrdenPago(\AppBundle\Entity\TipoOrdenPago $tipoOrdenPago = null)
    {
        $this->tipoOrdenPago = $tipoOrdenPago;

        return $this;
    }

    /**
     * Get tipoOrdenPago
     *
     * @return \AppBundle\Entity\TipoOrdenPago
     */
    public function getTipoOrdenPago()
    {
        return $this->tipoOrdenPago;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return OrdenDePago
     */
    public function setCreadoPor(\UsuariosBundle\Entity\Usuario $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $actualizadoPor
     *
     * @return OrdenDePago
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set decreto
     *
     * @param \AppBundle\Entity\Decreto $decreto
     *
     * @return OrdenDePago
     */
    public function setDecreto(\AppBundle\Entity\Decreto $decreto = null)
    {
        $this->decreto = $decreto;

        return $this;
    }

    /**
     * Get decreto
     *
     * @return \AppBundle\Entity\Decreto
     */
    public function getDecreto()
    {
        return $this->decreto;
    }
}
