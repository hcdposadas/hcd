<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
/**
 * PeriodoLegislativo
 *
 * @ORM\Table(name="periodo_legislativo")
 * @ORM\Entity(repositoryClass="App\Repository\PeriodoLegislativoRepository")
 * @Vich\Uploadable
 */
class PeriodoLegislativo extends BaseClass
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
     * @var string
     *
     * @ORM\Column(name="anio", type="string", length=255, unique=true)
     */
    private $anio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date")
     */
    private $fechaFin;

    /**
     * @var string
     *
     * @ORM\Column(name="frase", type="string", length=255)
     */
    private $frase;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $membrete;

	/**
	 * @Vich\UploadableField(mapping="varios", fileNameProperty="membrete")
	 * @var File
	 */
	private $membreteFile;

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return Documento
	 */
	public function setMembreteFile( File $file = null ) {
		$this->membreteFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
			$this->setFechaCreacion( new \DateTime('now'));
		}
	}

	/**
	 * @return File|null
	 */
	public function getMembreteFile() {
		return $this->membreteFile;
	}

	public function __toString() {
		return $this->anio;
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
     * Set anio
     *
     * @param string $anio
     *
     * @return PeriodoLegislativo
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return PeriodoLegislativo
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return PeriodoLegislativo
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set frase
     *
     * @param string $frase
     *
     * @return PeriodoLegislativo
     */
    public function setFrase($frase)
    {
        $this->frase = $frase;

        return $this;
    }

    /**
     * Get frase
     *
     * @return string
     */
    public function getFrase()
    {
        return $this->frase;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return PeriodoLegislativo
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
     * @return PeriodoLegislativo
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
     * @return PeriodoLegislativo
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
     * @return PeriodoLegislativo
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set membrete
     *
     * @param string $membrete
     *
     * @return PeriodoLegislativo
     */
    public function setMembrete($membrete)
    {
        $this->membrete = $membrete;

        return $this;
    }

    /**
     * Get membrete
     *
     * @return string
     */
    public function getMembrete()
    {
        return $this->membrete;
    }
}
