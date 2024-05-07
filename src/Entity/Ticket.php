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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaV;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaC;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $advertencia;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaCon;

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

    public function getFechaV(): ?\DateTimeInterface
    {
        return $this->fechaV;
    }

    public function setFechaV(?\DateTimeInterface $fechaV): self
    {
        $this->fechaV = $fechaV;

        return $this;
    }

    public function getFechaC(): ?\DateTimeInterface
    {
        return $this->fechaC;
    }

    public function setFechaC(?\DateTimeInterface $fechaC): self
    {
        $this->fechaC = $fechaC;

        return $this;
    }

    public function getConfirmado(): ?bool
    {
        return $this->confirmado;
    }

    public function setConfirmado(?bool $confirmado): self
    {
        $this->confirmado = $confirmado;

        return $this;
    }

    public function getAdvertencia(): ?string
    {
        return $this->advertencia;
    }

    public function setAdvertencia(?string $advertencia): self
    {
        $this->advertencia = $advertencia;

        return $this;
    }

    public function getFechaCon(): ?\DateTimeInterface
    {
        return $this->fechaCon;
    }

    public function setFechaCon(?\DateTimeInterface $fechaCon): self
    {
        $this->fechaCon = $fechaCon;

        return $this;
    }
}
