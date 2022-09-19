<?php

namespace App\Entity;

use App\Repository\PacienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PacienteRepository::class)
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
     * @ORM\OneToOne(targetEntity=Persona::class, inversedBy="paciente", cascade={"persist", "remove"})
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
}
