<?php

namespace App\Entity;

use App\Repository\ProveidoRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich; 
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=ProveidoRepository::class)
 */
class Proveido
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="caratula_proveido", fileNameProperty="caratula")
     * @var File
     */
    private $caratulaFile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $caratula;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha;

    /**
     * @Vich\UploadableField(mapping="proveido", fileNameProperty="archivo")
     * @var File
     */
    private $archivoFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $archivo;

    /**
     * @ORM\ManyToOne(targetEntity=Expediente::class, inversedBy="proveidos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expediente;

    /**
     * @Vich\UploadableField(mapping="proveido_cierre", fileNameProperty="cierre")
     * @var File
     */
    private $cierreFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cierre;


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return ProyectoBAE
     */
    public function setCaratulaFile(File $file = null)
    {
        $this->caratulaFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getCaratulaFile()
    {
        return $this->caratulaFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return ProyectoBAE
     */
    public function setArchivoFile(File $file = null)
    {
        $this->archivoFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getArchivoFile()
    {
        return $this->archivoFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return ProyectoBAE
     */
    public function setCierreFile(File $file = null)
    {
        $this->cierreFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getCierreFile()
    {
        return $this->cierreFile;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaratula(): ?string
    {
        return $this->caratula;
    }

    public function setCaratula(?string $caratula): self
    {
        $this->caratula = $caratula;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getExpediente(): ?Expediente
    {
        return $this->expediente;
    }

    public function setExpediente(?Expediente $expediente): self
    {
        $this->expediente = $expediente;

        return $this;
    }

    public function getCierre(): ?string
    {
        return $this->cierre;
    }

    public function setCierre(?string $cierre): self
    {
        $this->cierre = $cierre;

        return $this;
    }
}
