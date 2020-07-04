<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RamaRepository")
 */
class Rama extends BaseClass {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $titulo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $color;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $colorLetra;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $especial;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $numeroRomano;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $orden;

	public function __toString() {
		return $this->numeroRomano . ' - ' . $this->titulo;
	}
	

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Rama
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Rama
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set colorLetra
     *
     * @param string $colorLetra
     *
     * @return Rama
     */
    public function setColorLetra($colorLetra)
    {
        $this->colorLetra = $colorLetra;

        return $this;
    }

    /**
     * Get colorLetra
     *
     * @return string
     */
    public function getColorLetra()
    {
        return $this->colorLetra;
    }

    /**
     * Set especial
     *
     * @param boolean $especial
     *
     * @return Rama
     */
    public function setEspecial($especial)
    {
        $this->especial = $especial;

        return $this;
    }

    /**
     * Get especial
     *
     * @return boolean
     */
    public function getEspecial()
    {
        return $this->especial;
    }

    /**
     * Set numeroRomano
     *
     * @param string $numeroRomano
     *
     * @return Rama
     */
    public function setNumeroRomano($numeroRomano)
    {
        $this->numeroRomano = $numeroRomano;

        return $this;
    }

    /**
     * Get numeroRomano
     *
     * @return string
     */
    public function getNumeroRomano()
    {
        return $this->numeroRomano;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     *
     * @return Rama
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Rama
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
     * @return Rama
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
     * @return Rama
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
     * @return Rama
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
