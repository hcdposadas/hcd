<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @Vich\Uploadable
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

    /**
     * @Vich\UploadableField(mapping="ticket_adjunto", fileNameProperty="adjuntoNombre")
     */
    private $adjuntoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adjuntoNombre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(mappedBy: 'ticketPadre', targetEntity: Ticket::class)]
     */
    private Collection $ticketsRelacionados;

    /**
     * @ORM\ManyToOne(inversedBy: 'ticketsRelacionados')]
     */
    private ?Ticket $ticketPadre = null;

    public function __construct()
    {
        $this->ticketsRelacionados = new ArrayCollection();
    }

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

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $adjuntoFile
     */
    public function setAdjuntoFile(?File $adjuntoFile = null): void
    {
        $this->adjuntoFile = $adjuntoFile;

        if (null !== $adjuntoFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getAdjuntoFile(): ?File
    {
        return $this->adjuntoFile;
    }

    public function setAdjuntoNombre(?string $adjuntoNombre): self
    {
        $this->adjuntoNombre = $adjuntoNombre;

        return $this;
    }

    public function getAdjuntoNombre(): ?string
    {
        return $this->adjuntoNombre;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTicketsRelacionados(): Collection
    {
        return $this->ticketsRelacionados;
    }

    public function addTicketRelacionado(Ticket $ticketRelacionado): self
    {
        if (!$this->ticketsRelacionados->contains($ticketRelacionado)) {
            $this->ticketsRelacionados->add($ticketRelacionado);
            $ticketRelacionado->setTicketPadre($this);
        }

        return $this;
    }

    public function removeTicketRelacionado(Ticket $ticketRelacionado): self
    {
        if ($this->ticketsRelacionados->removeElement($ticketRelacionado)) {
            // set the owning side to null (unless already changed)
            if ($ticketRelacionado->getTicketPadre() === $this) {
                $ticketRelacionado->setTicketPadre(null);
            }
        }

        return $this;
    }

    public function getTicketPadre(): ?Ticket
    {
        return $this->ticketPadre;
    }

    public function setTicketPadre(?Ticket $ticketPadre): self
    {
        $this->ticketPadre = $ticketPadre;

        return $this;
    }
}
