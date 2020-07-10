<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalLicenciaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PersonalLicenciaRepository::class)
 * @Vich\Uploadable
 */
class PersonalLicencia extends BaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaDesde;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaHasta;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalArticulo::class)
     */
    private $articulo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $archivo;

	/**
	 * @Vich\UploadableField(mapping="rrhh_licencia", fileNameProperty="archivo")
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
	 * @return PersonalLicencia
	 */
	public function setArchivoFile( File $file = null ) {
		$this->archivoFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
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

    public function getFechaDesde(): ?\DateTimeInterface
    {
        return $this->fechaDesde;
    }

    public function setFechaDesde(\DateTimeInterface $fechaDesde): self
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    public function getFechaHasta(): ?\DateTimeInterface
    {
        return $this->fechaHasta;
    }

    public function setFechaHasta(\DateTimeInterface $fechaHasta): self
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    public function getArticulo(): ?PersonalArticulo
    {
        return $this->articulo;
    }

    public function setArticulo(?PersonalArticulo $articulo): self
    {
        $this->articulo = $articulo;

        return $this;
    }

	/**
	 * @return string
	 */
	public function getArchivo(): string {
		return $this->archivo;
	}

	/**
	 * @param string $archivo
	 */
	public function setArchivo( string $archivo ): void {
		$this->archivo = $archivo;
	}


}
