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
     * @ORM\OneToMany(targetEntity=Orden::class, mappedBy="paciente")
     */
    private $ordens;

    public function __construct()
    {
        $this->ordens = new ArrayCollection();
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
     * @return Collection|Orden[]
     */
    public function getOrdens(): Collection
    {
        return $this->ordens;
    }

    public function addOrden(Orden $orden): self
    {
        if (!$this->ordens->contains($orden)) {
            $this->ordens[] = $orden;
            $orden->setPaciente($this);
        }

        return $this;
    }

    public function removeOrden(Orden $orden): self
    {
        if ($this->ordens->contains($orden)) {
            $this->ordens->removeElement($orden);
            // set the owning side to null (unless already changed)
            if ($orden->getPaciente() === $this) {
                $orden->setPaciente(null);
            }
        }

        return $this;
    }
}
