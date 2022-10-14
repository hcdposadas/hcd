<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrdenMedicaRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity(repositoryClass=OrdenMedicaRepository::class)
 * @Vich\Uploadable
 */
class OrdenMedica
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Paciente::class, inversedBy="ordenMedicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paciente;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $medicoOtorgante;

    /**
     * @ORM\Column(type="date")
     */
    private $desde;

    /**
     * @ORM\Column(type="date")
     */
    private $hasta;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalArticulo::class, inversedBy="ordenMedicas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $diagnostico;

    /**
     * @Vich\UploadableField(mapping="rrmm_diagnostico", fileNameProperty="diagnostico")
     * @var File
     */
    private $diagnosticoFile;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @return OrdenMedica
     */
    public function setDiagnosticoFile(File $file = null)
    {
        $this->diagnosticoFile = $file;

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
    public function getDiagnosticoFile()
    {
        return $this->diagnosticoFile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaciente(): ?Paciente
    {
        return $this->paciente;
    }

    public function setPaciente(?Paciente $paciente): self
    {
        $this->paciente = $paciente;

        return $this;
    }

    public function getMedicoOtorgante(): ?string
    {
        return $this->medicoOtorgante;
    }

    public function setMedicoOtorgante(string $medicoOtorgante): self
    {
        $this->medicoOtorgante = $medicoOtorgante;

        return $this;
    }

    public function getDesde(): ?\DateTimeInterface
    {
        return $this->desde;
    }

    public function setDesde(\DateTimeInterface $desde): self
    {
        $this->desde = $desde;

        return $this;
    }

    public function getHasta(): ?\DateTimeInterface
    {
        return $this->hasta;
    }

    public function setHasta(\DateTimeInterface $hasta): self
    {
        $this->hasta = $hasta;

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

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function setDiagnostico(string $diagnostico): self
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }
}
