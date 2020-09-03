<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalNovedadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PersonalNovedadRepository::class)
 * @Vich\Uploadable
 */
class PersonalNovedad extends BaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $observacion;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Legajo::class, inversedBy="personalNovedads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $legajo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $archivo;

	/**
	 * @Vich\UploadableField(mapping="rrhh_novedad", fileNameProperty="archivo")
	 * @var File
	 */
	private $archivoFile;

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return PersonalNovedad
	 */
	public function setArchivoFile( File $file = null ) {
		$this->archivoFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getArchivoFile() {
		return $this->archivoFile;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getLegajo(): ?Legajo
    {
        return $this->legajo;
    }

    public function setLegajo(?Legajo $legajo): self
    {
        $this->legajo = $legajo;

        return $this;
    }

	/**
	 * @return string
	 */
	public function getArchivo(): ?string {
		return $this->archivo;
	}

	/**
	 * @param string $archivo
	 */
	public function setArchivo( string $archivo ): void {
		$this->archivo = $archivo;
	}


}
