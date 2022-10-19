<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PacienteRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="paciente")
 * @ORM\Entity(repositoryClass=App\Repository\PacienteRepository::class)
 * @Vich\Uploadable
 */
class Paciente
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Persona::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $persona;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $grupo;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $factor;

    /**
     * @ORM\OneToMany(targetEntity=OrdenMedica::class, mappedBy="paciente")
     */
    private $ordenMedicas;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @Vich\UploadableField(mapping="rrmm_perfil", fileNameProperty="foto")
     * @var File
     */
    private $fotoFile;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Paciente
     */
    public function setFotoFile(File $file = null)
    {
        $this->fotoFile = $file;

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
    public function getFotoFile()
    {
        return $this->fotoFile;
    }

    public function __construct()
    {
        $this->ordenMedicas = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(Persona $persona): self
    {
        $this->persona = $persona;

        return $this;
    }

    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function setGrupo(?string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function getFactor(): ?string
    {
        return $this->factor;
    }

    public function setFactor(string $factor): self
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * @return Collection|OrdenMedica[]
     */
    public function getOrdenMedicas(): Collection
    {
        return $this->ordenMedicas;
    }

    public function addOrdenMedica(OrdenMedica $ordenMedica): self
    {
        if (!$this->ordenMedicas->contains($ordenMedica)) {
            $this->ordenMedicas[] = $ordenMedica;
            $ordenMedica->setPaciente($this);
        }

        return $this;
    }

    public function removeOrdenMedica(OrdenMedica $ordenMedica): self
    {
        if ($this->ordenMedicas->contains($ordenMedica)) {
            $this->ordenMedicas->removeElement($ordenMedica);
            // set the owning side to null (unless already changed)
            if ($ordenMedica->getPaciente() === $this) {
                $ordenMedica->setPaciente(null);
            }
        }

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }
}

