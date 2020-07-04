<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Usuario;
use App\Entity\Base\BaseClass;

/**
 * Voto
 *
 * @ORM\Table(name="voto")
 * @ORM\Entity(repositoryClass="App\Repository\VotoRepository")
 */
class Voto extends BaseClass
{
    const VOTO_AFIRMATIVO = 1;
    const VOTO_ABSTENCION = 0;
    const VOTO_NEGATIVO = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Usuario $concejal
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
     */
    private $concejal;

    /**
     * @var Mocion $mocion
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Mocion", inversedBy="votos")
     */
    private $mocion;

    /**
     * @var Mocion $votacion
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Votacion", inversedBy="votos")
     * @ORM\JoinColumn(name="votacion_id", referencedColumnName="id", nullable=true)
     */
    private $votacion;

    /**
     * @var int
     *
     * @ORM\Column(name="valor", type="integer")
     */
    private $valor;

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getValor() == self::VOTO_AFIRMATIVO) {
            return 'SI';
        } elseif ($this->getValor() == self::VOTO_NEGATIVO) {
            return 'NO';
        } else {
            return '?';
        }
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
     * @return Usuario
     */
    public function getConcejal()
    {
        return $this->concejal;
    }

    /**
     * @param Usuario $concejal
     */
    public function setConcejal($concejal)
    {
        $this->concejal = $concejal;
    }

    /**
     * @return Mocion
     */
    public function getMocion()
    {
        return $this->mocion;
    }

    /**
     * @param Mocion $mocion
     */
    public function setMocion($mocion)
    {
        $this->mocion = $mocion;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     *
     * @return Voto
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return int
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @return Mocion
     */
    public function getVotacion()
    {
        return $this->votacion;
    }

    /**
     * @param Mocion $votacion
     */
    public function setVotacion($votacion)
    {
        $this->votacion = $votacion;
    }

    /**
     * @return bool
     */
    public function esAfirmativo()
    {
        return $this->getValor() == self::VOTO_AFIRMATIVO;
    }

    /**
     * @return bool
     */
    public function esNegativo()
    {
        return $this->getValor() == self::VOTO_NEGATIVO;
    }

    /**
     * @return bool
     */
    public function esAbstencion()
    {
        return $this->getValor() == self::VOTO_ABSTENCION;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Voto
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
     * @return Voto
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return Voto
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
     * @return Voto
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
