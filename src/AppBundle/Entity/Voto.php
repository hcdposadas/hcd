<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Voto
 *
 * @ORM\Table(name="voto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VotoRepository")
 */
class Voto extends BaseClass
{
    const VOTO_SI = 1;
    const VOTO_NO = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Mocion $mocion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mocion", inversedBy="votos")
     */
    private $mocion;

    /**
     * @var Mocion $votacion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Votacion", inversedBy="votos")
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
        if ($this->getValor() == self::VOTO_SI) {
            return 'SI';
        } elseif ($this->getValor() == self::VOTO_NO) {
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
}

