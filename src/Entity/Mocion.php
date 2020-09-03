<?php

namespace App\Entity;

use App\Repository\ParametroRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Expediente;
use App\Entity\Base\BaseClass;

/**
 * Mocion
 *
 * @ORM\Table(
 *     name="mocion",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="numero_unico_idx",
 *             columns={"sesion_id", "numero"}
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\MocionRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Sesion", inversedBy="mociones")
     */
    private $sesion;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var Parametro $tipo
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Parametro")
     */
    private $tipo;

    /**
     * @var TipoMayoria $tipoMocion
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoMayoria")
     */
    private $tipoMayoria;

    /**
     * @var boolean $aprobado
     *
     * @ORM\Column(name="aprobado", type="boolean", nullable=true)
     */
    private $aprobado = null;

    /**
     * @var Expediente $expediente
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Expediente")
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
     */
    private $expediente;

    /**
     * @var Parametro $estado
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Parametro")
     */
    private $estado;

    /**
     * @var int $cuentaTotal
     *
     * @ORM\Column(name="cuenta_total", type="integer", nullable=true)
     */
    private $cuentaTotal;

    /**
     * @var int $cuentaAfirmativos
     *
     * @ORM\Column(name="cuentaAfirmativos", type="integer", nullable=true)
     */
    private $cuentaAfirmativos;

    /**
     * @var int $cuentaNegativos
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
     * @ORM\OneToMany(targetEntity="App\Entity\Votacion", mappedBy="mocion")
     */
    private $votaciones;

    /**
     * @var ArrayCollection|Voto[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Voto", mappedBy="mocion")
     */
    private $votos;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string",length=255, nullable=true)
     */
    private $texto;

    public function __construct()
    {
        $this->votaciones = new ArrayCollection();
        $this->votos = new ArrayCollection();
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
     * @return bool
     */
    public function isAprobado()
    {
        return $this->aprobado;
    }

    /**
     * @param bool $aprobado
     */
    public function setAprobado($aprobado)
    {
        $this->aprobado = $aprobado;
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
     * @return int
     */
    public function getCuentaTotal()
    {
        return $this->cuentaTotal;
    }

    /**
     * @param int $cuentaTotal
     */
    public function setCuentaTotal($cuentaTotal)
    {
        $this->cuentaTotal = $cuentaTotal;
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
     * @return Voto[]|ArrayCollection
     */
    public function getVotos()
    {
        return $this->votos;
    }

    /**
     * @param Voto[]|ArrayCollection $votos
     */
    public function setVotos($votos)
    {
        $this->votos = $votos;
    }

    /**
     * @return bool
     */
    public function puedeVotarse()
    {
        if ($this->getEstado()) {
            return $this->getEstado()->getSlug() == self::ESTADO_PARA_VOTAR;
        }

        return false;

    }

    /**
     * @return bool
     */
    public function enVotacion()
    {
        return $this->getEstado()->getSlug() == self::ESTADO_EN_VOTACION;
    }

    public function tiempoDeVotacionRestante()
    {
        foreach ($this->getVotaciones() as $votacion) {
            if (!$votacion->finalizada()) {
                $fecha = clone $votacion->getFechaCreacion();
                $diff = $fecha->modify('+' . $votacion->getDuracion() . ' seconds')->diff(new DateTime());

                return $diff->s;
            }
        }

        return null;
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

    /**
     * Get aprobado
     *
     * @return boolean
     */
    public function getAprobado()
    {
        return $this->aprobado;
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return Mocion
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Mocion
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
     * @return Mocion
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add votacione
     *
     * @param \App\Entity\Votacion $votacione
     *
     * @return Mocion
     */
    public function addVotacione(\App\Entity\Votacion $votacione)
    {
        $this->votaciones[] = $votacione;

        return $this;
    }

    /**
     * Remove votacione
     *
     * @param \App\Entity\Votacion $votacione
     */
    public function removeVotacione(\App\Entity\Votacion $votacione)
    {
        $this->votaciones->removeElement($votacione);
    }

    /**
     * Add voto
     *
     * @param \App\Entity\Voto $voto
     *
     * @return Mocion
     */
    public function addVoto(\App\Entity\Voto $voto)
    {
        $this->votos[] = $voto;

        return $this;
    }

    /**
     * Remove voto
     *
     * @param \App\Entity\Voto $voto
     */
    public function removeVoto(\App\Entity\Voto $voto)
    {
        $this->votos->removeElement($voto);
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return Mocion
     */
    public function setCreadoPor(\App\Entity\Usuario $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \App\Entity\Usuario $actualizadoPor
     *
     * @return Mocion
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
