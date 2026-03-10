<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class ComunicacionArchivo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Comunicacion::class, inversedBy="archivos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comunicacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archivo;

    /**
     * @Vich\UploadableField(mapping="comunicados", fileNameProperty="archivo")
     * @var File
     */
    private $archivoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombreOriginal;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaSubida;

    public function __toString()
    {
        return $this->archivo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComunicacion(): ?Comunicacion
    {
        return $this->comunicacion;
    }

    public function setComunicacion(?Comunicacion $comunicacion): self
    {
        $this->comunicacion = $comunicacion;
        return $this;
    }

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(?string $archivo): self
    {
        $this->archivo = $archivo;
        return $this;
    }

    public function setArchivoFile(?File $archivo = null): void
    {
        $this->archivoFile = $archivo;
        if ($archivo) {
            $this->fechaSubida = new \DateTime();
            if ($archivo instanceof UploadedFile) {
                $this->nombreOriginal = $archivo->getClientOriginalName();
            }
        }
    }

    public function getArchivoFile(): ?File
    {
        return $this->archivoFile;
    }

    public function getNombreOriginal(): ?string
    {
        return $this->nombreOriginal;
    }

    public function setNombreOriginal(?string $nombreOriginal): self
    {
        $this->nombreOriginal = $nombreOriginal;
        return $this;
    }

    public function getFechaSubida(): ?\DateTimeInterface
    {
        return $this->fechaSubida;
    }

    public function setFechaSubida(?\DateTimeInterface $fechaSubida): self
    {
        $this->fechaSubida = $fechaSubida;
        return $this;
    }
}
