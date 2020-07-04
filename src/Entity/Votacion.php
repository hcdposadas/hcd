<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * Votacion
 *
 * @ORM\Table(name="votacion")
 * @ORM\Entity(repositoryClass="App\Repository\VotacionRepository")
 */
class Votacion extends BaseClass
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
     * @var Mocion $mocion
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Mocion", inversedBy="votaciones")
     */
    private $mocion;

    /**
     * @var int
     *
     * @ORM\Column(name="duracion", type="integer")
     */
    private $duracion;

    /**
     * @var bool
     *
     * @ORM\Column(name="extension", type="boolean", options={"default": false})
     */
    private $extension = false;

    /**
     * @var ArrayCollection|Voto[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Voto", mappedBy="votacion")
     */
    private $votos;

	public function __toString() {
		return (string) $this->id;
	}

    public function __construct()
    {
        $this->votos = new ArrayCollection();
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
     * Set extension
     *
     * @param boolean $extension
     *
     * @return Votacion
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return bool
     */
    public function getExtension()
    {
        return $this->extension;
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
    public function finalizada()
    {
        $fecha = clone $this->getFechaCreacion();
        return $fecha->modify('+'.$this->getDuracion().' seconds') < new DateTime();
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Votacion
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
     * @return Votacion
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add voto
     *
     * @param \App\Entity\Voto $voto
     *
     * @return Votacion
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
     * @return Votacion
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
     * @return Votacion
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
