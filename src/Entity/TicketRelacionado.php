<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRelacionadoRepository")
 * @ORM\Table(name="ticket_relacionado")
 */
class TicketRelacionado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket", inversedBy="ticketsOrigenRelacionados")
     * @ORM\JoinColumn(name="ticket_origen_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Debe seleccionar un ticket de origen")
     */
    private $ticketOrigen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket", inversedBy="ticketsDestinoRelacionados")
     * @ORM\JoinColumn(name="ticket_destino_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Debe seleccionar un ticket de destino")
     */
    private $ticketDestino;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCreacion;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tipoRelacion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketOrigen(): ?Ticket
    {
        return $this->ticketOrigen;
    }

    public function setTicketOrigen(?Ticket $ticketOrigen): self
    {
        $this->ticketOrigen = $ticketOrigen;

        return $this;
    }

    public function getTicketDestino(): ?Ticket
    {
        return $this->ticketDestino;
    }

    public function setTicketDestino(?Ticket $ticketDestino): self
    {
        $this->ticketDestino = $ticketDestino;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getTipoRelacion(): ?string
    {
        return $this->tipoRelacion;
    }

    public function setTipoRelacion(?string $tipoRelacion): self
    {
        $this->tipoRelacion = $tipoRelacion;

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
} 