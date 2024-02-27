<?php

namespace App\Entity;

use App\Repository\RecibidoComunicadoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecibidoComunicadoRepository::class)
 */
class RecibidoComunicado
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Comunicacion::class, inversedBy="recibidoComunicados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comunicacion;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class, inversedBy="recibidoComunicados")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComunicacion(): ?Comunicacion
    {
        return $this->comunicacion;
    }

    public function setComunicacion(?Comunicacion $comunicacion): self
    {
        $this->comunicacion = $comunicacion;

        return $this;
    }

    public function getArea(): ?AreaAdministrativa
    {
        return $this->area;
    }

    public function setArea(?AreaAdministrativa $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
}
