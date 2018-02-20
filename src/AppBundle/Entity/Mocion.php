<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ParametroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MesaEntradaBundle\Entity\Expediente;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Mocion
 *
 * @ORM\Table(name="mocion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MocionRepository")
 */
class Mocion extends BaseClass
{
    const ESTADO_PARA_VOTAR = 'mocion-estados-para-votar';
    const ESTADO_EN_VOTACION = 'mocion-estados-en-votacion';
    const ESTADO_FINALIZADO = 'mocion-estados-finalizado';

    const TIPO_PLANIFICADA = 'mocion-tipo-planificada';
    const TIPO_ESPONTANEA = 'mocion-tipo-espontanea';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Sesion $sesion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sesion", inversedBy="mociones")
     */
    private $sesion;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer", unique=true)
     */
    private $numero;

    /**
     * @var Parametro $tipo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Parametro")
     */
    private $tipo;

    /**
     * @var TipoMayoria $tipoMocion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoMayoria")
     */
    private $tipoMayoria;

    /**
     * @var Expediente $expediente
     *
     * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente")
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
     */
    private $expediente;

    /**
     * @var Parametro $estado
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Parametro")
     */
    private $estado;

    /**
     * @var int
     *
     * @ORM\Column(name="cuentaAfirmativos", type="integer", nullable=true)
     */
    private $cuentaAfirmativos;

    /**
     * @var int
     *
     * @ORM\Column(name="cuentaNegativos", type="integer", nullable=true)
     */
    private $cuentaNegativos;

    /**
     * @var int
     *
     * @ORM\Column(name="cuentaAbstenciones", type="integer", nullable=true)
     */
    private $cuentaAbstenciones;

    /**
     * @var ArrayCollection|Votacion[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Votacion", mappedBy="mocion")
     */
    private $votaciones;

    public function __construct()
    {
        $this->votaciones = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '' . $this->getNumero();
    }


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
     * @return Sesion
     */
    public function getSesion()
    {
        return $this->sesion;
    }

    /**
     * @param Sesion $sesion
     */
    public function setSesion($sesion)
    {
        $this->sesion = $sesion;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Mocion
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
     * @return Parametro
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param Parametro $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return TipoMayoria
     */
    public function getTipoMayoria()
    {
        return $this->tipoMayoria;
    }

    /**
     * @param TipoMayoria $tipoMayoria
     */
    public function setTipoMayoria($tipoMayoria)
    {
        $this->tipoMayoria = $tipoMayoria;
    }

    /**
     * @return Expediente
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * @param Expediente $expediente
     */
    public function setExpediente($expediente)
    {
        $this->expediente = $expediente;
    }

    /**
     * @return Parametro
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param Parametro $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Set cuentaAfirmativos
     *
     * @param integer $cuentaAfirmativos
     *
     * @return Mocion
     */
    public function setCuentaAfirmativos($cuentaAfirmativos)
    {
        $this->cuentaAfirmativos = $cuentaAfirmativos;

        return $this;
    }

    /**
     * Get cuentaAfirmativos
     *
     * @return int
     */
    public function getCuentaAfirmativos()
    {
        return $this->cuentaAfirmativos;
    }

    /**
     * Set cuentaNegativos
     *
     * @param integer $cuentaNegativos
     *
     * @return Mocion
     */
    public function setCuentaNegativos($cuentaNegativos)
    {
        $this->cuentaNegativos = $cuentaNegativos;

        return $this;
    }

    /**
     * Get cuentaNegativos
     *
     * @return int
     */
    public function getCuentaNegativos()
    {
        return $this->cuentaNegativos;
    }

    /**
     * Set cuentaAbstenciones
     *
     * @param integer $cuentaAbstenciones
     *
     * @return Mocion
     */
    public function setCuentaAbstenciones($cuentaAbstenciones)
    {
        $this->cuentaAbstenciones = $cuentaAbstenciones;

        return $this;
    }

    /**
     * Get cuentaAbstenciones
     *
     * @return int
     */
    public function getCuentaAbstenciones()
    {
        return $this->cuentaAbstenciones;
    }

    /**
     * @return Votacion[]|ArrayCollection
     */
    public function getVotaciones()
    {
        return $this->votaciones;
    }

    /**
     * @param Votacion[]|ArrayCollection $votaciones
     */
    public function setVotaciones($votaciones)
    {
        $this->votaciones = $votaciones;
    }

    /**
     * @return bool
     */
    public function puedeVotarse()
    {
        return $this->getEstado()->getSlug() == self::ESTADO_PARA_VOTAR;
    }

    /**
     * @return bool
     */
    public function enVotacion()
    {
        return $this->getEstado()->getSlug() == self::ESTADO_EN_VOTACION;
    }

    /**
     * @return bool
     */
    public function finalizada()
    {
        return $this->getEstado()->getSlug() == self::ESTADO_FINALIZADO;
    }

    /**
     * @return bool
     */
    public function editable()
    {
        return $this->puedeVotarse();
    }
}

