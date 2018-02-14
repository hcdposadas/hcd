<?php

namespace VotacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Voto
 *
 * @ORM\Table(name="voto")
 * @ORM\Entity(repositoryClass="VotacionBundle\Repository\VotoRepository")
 */
class Voto
{
    const ABSTENCION = 0;
    const AFIRMATIVO = 1;
    const NEGATIVO = -1;

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
     * @ORM\Column(name="valor", type="integer")
     */
    private $valor;

    /**
     * @var Votacion $votacion
     *
     * @ORM\ManyToOne(targetEntity="VotacionBundle\Entity\Votacion", inversedBy="votos")
     */
    private $votacion;


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
     * @return Votacion
     */
    public function getVotacion()
    {
        return $this->votacion;
    }

    /**
     * @param Votacion $votacion
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
        return $this->getValor() == self::AFIRMATIVO;
    }

    /**
     * @return bool
     */
    public function esNegativo()
    {
        return $this->getValor() == self::NEGATIVO;
    }

    /**
     * @return bool
     */
    public function esAbstencion()
    {
        return $this->getValor() == self::ABSTENCION;
    }
}

