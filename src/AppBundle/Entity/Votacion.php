<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Votacion
 *
 * @ORM\Table(name="votacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VotacionRepository")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mocion", inversedBy="votaciones")
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Voto", mappedBy="votacion")
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
}

