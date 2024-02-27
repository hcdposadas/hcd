<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * AreaAdministrativa
 *
 * @ORM\Table(name="area_administrativa")
 * @ORM\Entity(repositoryClass="App\Repository\AreaAdministrativaRepository")
 */
class AreaAdministrativa extends BaseClass
{
    const AREA_ADMINISTRATIVA_DEM = 29;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="areaOrigen")
     */
    private $ticketsO;

        /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="areaDestino")
     */
    private $ticketsD;

    /**
     * @ORM\OneToMany(targetEntity=Comunicacion::class, mappedBy="areaOrigen")
     */
    private $comunicacions;

    /**
     * @ORM\ManyToMany(targetEntity=Comunicacion::class, mappedBy="areaDestino")
     */
    private $comunicacionsR;

    /**
     * @ORM\OneToMany(targetEntity=RecibidoComunicado::class, mappedBy="area")
     */
    private $recibidoComunicados;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->comunicacions = new ArrayCollection();
        $this->comunicacionsR = new ArrayCollection();
        $this->recibidoComunicados = new ArrayCollection();
    }

	public function __toString() {
                                                                     		return $this->nombre;
                                                                     	}


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return AreaAdministrativa
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return AreaAdministrativa
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return AreaAdministrativa
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Set fechaActualizacion
     *
     * @param \DateTime $fechaActualizacion
     * @return AreaAdministrativa
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     * @return AreaAdministrativa
     */
    public function setCreadoPor(\App\Entity\Usuario $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \App\Entity\Usuario $actualizadoPor
     * @return AreaAdministrativa
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTicketsO(): Collection
    {
        return $this->ticketsO;
    }

    public function addTicketO(Ticket $ticket): self
    {
        if (!$this->ticketsO->contains($ticket)) {
            $this->ticketsO[] = $ticket;
            $ticket->setAreaOrigen($this);
        }

        return $this;
    }

    public function removeTicketO(Ticket $ticket): self
    {
        if ($this->ticketsO->contains($ticket)) {
            $this->ticketsO->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getAreaOrigen() === $this) {
                $ticket->setAreaOrigen(null);
            }
        }

        return $this;
    }

        /**
     * @return Collection|Ticket[]
     */
    public function getTicketsD(): Collection
    {
        return $this->ticketsD;
    }

    public function addTicketD(Ticket $ticket): self
    {
        if (!$this->ticketsD->contains($ticket)) {
            $this->ticketsD[] = $ticket;
            $ticket->setAreaOrigen($this);
        }

        return $this;
    }

    public function removeTicketD(Ticket $ticket): self
    {
        if ($this->ticketsD->contains($ticket)) {
            $this->ticketsD->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getAreaOrigen() === $this) {
                $ticket->setAreaOrigen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comunicacion[]
     */
    public function getComunicacions(): Collection
    {
        return $this->comunicacions;
    }

    public function addComunicacion(Comunicacion $comunicacion): self
    {
        if (!$this->comunicacions->contains($comunicacion)) {
            $this->comunicacions[] = $comunicacion;
            $comunicacion->setAreaOrigen($this);
        }

        return $this;
    }

    public function removeComunicacion(Comunicacion $comunicacion): self
    {
        if ($this->comunicacions->contains($comunicacion)) {
            $this->comunicacions->removeElement($comunicacion);
            // set the owning side to null (unless already changed)
            if ($comunicacion->getAreaOrigen() === $this) {
                $comunicacion->setAreaOrigen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comunicacion[]
     */
    public function getComunicacionsR(): Collection
    {
        return $this->comunicacionsR;
    }

    public function addComunicacionsR(Comunicacion $comunicacionsR): self
    {
        if (!$this->comunicacionsR->contains($comunicacionsR)) {
            $this->comunicacionsR[] = $comunicacionsR;
            $comunicacionsR->addAreaDestino($this);
        }

        return $this;
    }

    public function removeComunicacionsR(Comunicacion $comunicacionsR): self
    {
        if ($this->comunicacionsR->contains($comunicacionsR)) {
            $this->comunicacionsR->removeElement($comunicacionsR);
            $comunicacionsR->removeAreaDestino($this);
        }

        return $this;
    }

    /**
     * @return Collection|RecibidoComunicado[]
     */
    public function getRecibidoComunicados(): Collection
    {
        return $this->recibidoComunicados;
    }

    public function addRecibidoComunicado(RecibidoComunicado $recibidoComunicado): self
    {
        if (!$this->recibidoComunicados->contains($recibidoComunicado)) {
            $this->recibidoComunicados[] = $recibidoComunicado;
            $recibidoComunicado->setArea($this);
        }

        return $this;
    }

    public function removeRecibidoComunicado(RecibidoComunicado $recibidoComunicado): self
    {
        if ($this->recibidoComunicados->contains($recibidoComunicado)) {
            $this->recibidoComunicados->removeElement($recibidoComunicado);
            // set the owning side to null (unless already changed)
            if ($recibidoComunicado->getArea() === $this) {
                $recibidoComunicado->setArea(null);
            }
        }

        return $this;
    }
}
