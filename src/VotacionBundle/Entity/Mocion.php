<?php

namespace VotacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mocion
 *
 * @ORM\Table(name="mocion")
 * @ORM\Entity(repositoryClass="VotacionBundle\Repository\MocionRepository")
 */
class Mocion
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $numero
     *
     * @ORM\Column(name="numero", type="string")
     */
    private $numero;

    /**
     * @var \DateTime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var ArrayCollection $votaciones
     *
     * @ORM\OneToMany(targetEntity="VotacionBundle\Entity\Votacion", mappedBy="mocion")
     */
    private $votaciones;

    /**
     * @var integer $estado
     *
     * @ORM\Column(name="estado")
     */
    private $estado;

    /**
     * @var integer $cuentaAfirmativos
     *
     * @ORM\Column(name="cuenta_afirmativos", type="integer", nullable=true)
     */
    private $cuentaAfirmativos;

    /**
     * @var integer $cuentaNegativos
     *
     * @ORM\Column(name="cuenta_negativos", type="integer", nullable=true)
     */
    private $cuentaNegativos;

    /**
     * @var integer $cuentaAbstenciones
     *
     * @ORM\Column(name="cuenta_abstenciones", type="integer", nullable=true)
     */
    private $cuentaAbstenciones;

    private $resultado;

    public function __toString()
    {
        return $this->getNumero();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return ArrayCollection
     */
    public function getVotaciones()
    {
        return $this->votaciones;
    }

    /**
     * @param ArrayCollection $votaciones
     */
    public function setVotaciones($votaciones)
    {
        $this->votaciones = $votaciones;
    }

    /**
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param int $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return int
     */
    public function getCuentaAfirmativos()
    {
        return $this->cuentaAfirmativos;
    }

    /**
     * @param int $cuentaAfirmativos
     */
    public function setCuentaAfirmativos($cuentaAfirmativos)
    {
        $this->cuentaAfirmativos = $cuentaAfirmativos;
    }

    /**
     * @return int
     */
    public function getCuentaNegativos()
    {
        return $this->cuentaNegativos;
    }

    /**
     * @param int $cuentaNegativos
     */
    public function setCuentaNegativos($cuentaNegativos)
    {
        $this->cuentaNegativos = $cuentaNegativos;
    }

    /**
     * @return int
     */
    public function getCuentaAbstenciones()
    {
        return $this->cuentaAbstenciones;
    }

    /**
     * @param int $cuentaAbstenciones
     */
    public function setCuentaAbstenciones($cuentaAbstenciones)
    {
        $this->cuentaAbstenciones = $cuentaAbstenciones;
    }

    /**
     * @return mixed
     */
    public function getResultado()
    {
        return $this->resultado;
    }

    /**
     * @param mixed $resultado
     */
    public function setResultado($resultado)
    {
        $this->resultado = $resultado;
    }
}

