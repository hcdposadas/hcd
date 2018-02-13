<?php

namespace VotacionBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Votacion
 *
 * @ORM\Table(name="votacion")
 * @ORM\Entity(repositoryClass="VotacionBundle\Repository\VotacionRepository")
 */
class Votacion
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
     * @var \DateTime
     *
     * @ORM\Column(name="desde", type="datetime")
     */
    private $desde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hasta", type="datetime")
     */
    private $hasta;

    /**
     * @var int
     *
     * @ORM\Column(name="duracion", type="integer")
     */
    private $duracion;

    /**
     * @var Mocion $mocion
     *
     * @ORM\ManyToOne(targetEntity="VotacionBundle\Entity\Mocion", inversedBy="votaciones")
     */
    private $mocion;

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
     * Set desde
     *
     * @param \DateTime $desde
     *
     * @return Votacion
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;

        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     *
     * @return Votacion
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set duracion
     *
     * @param integer $duracion
     *
     * @return Votacion
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion
     *
     * @return int
     */
    public function getDuracion()
    {
        return $this->duracion;
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

    public function esActiva(DateTime $fecha = null)
    {
        $fecha = $fecha ? $fecha : new DateTime();
        return $this->getDesde() >= $fecha && $fecha <= $this->getHasta();
    }
}

