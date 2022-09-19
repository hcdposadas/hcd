<?php

namespace App\Entity;

use App\Repository\OrdenMedicaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdenMedicaRepository::class)
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
     * @ORM\Column(type="string", length=255)
     */
    private $diagnostico;

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
