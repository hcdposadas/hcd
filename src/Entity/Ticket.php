<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $texto;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class, inversedBy="ticketsO")
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaOrigen;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class, inversedBy="ticketsD")
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaDestino;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $completo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="boolean")
     */
    private $abierto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

    public function getAreaOrigen(): ?AreaAdministrativa
    {
        return $this->areaOrigen;
    }

    public function setAreaOrigen(?AreaAdministrativa $areaOrigen): self
    {
        $this->areaOrigen = $areaOrigen;

        return $this;
    }

    public function getAreaDestino(): ?AreaAdministrativa
    {
        return $this->areaDestino;
    }

    public function setAreaDestino(?AreaAdministrativa $areaDestino): self
    {
        $this->areaDestino = $areaDestino;

        return $this;
    }

    public function getCompleto(): ?bool
    {
        return $this->completo;
    }

    public function setCompleto(bool $completo): self
    {
        $this->completo = $completo;

        return $this;
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

    public function getAbierto(): ?bool
    {
        return $this->abierto;
    }

    public function setAbierto(bool $abierto): self
    {
        $this->abierto = $abierto;

        return $this;
    }
}
