<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalLugarTrabajoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalLugarTrabajoRepository::class)
 */
class PersonalLugarTrabajo extends BaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaDesde;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaHasta;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actual;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaAdministrativa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaDesde(): ?\DateTimeInterface
    {
        return $this->fechaDesde;
    }

    public function setFechaDesde(\DateTimeInterface $fechaDesde): self
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    public function getFechaHasta(): ?\DateTimeInterface
    {
        return $this->fechaHasta;
    }

    public function setFechaHasta(?\DateTimeInterface $fechaHasta): self
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    public function getActual(): ?bool
    {
        return $this->actual;
    }

    public function setActual(bool $actual): self
    {
        $this->actual = $actual;

        return $this;
    }

    public function getAreaAdministrativa(): ?AreaAdministrativa
    {
        return $this->areaAdministrativa;
    }

    public function setAreaAdministrativa(?AreaAdministrativa $areaAdministrativa): self
    {
        $this->areaAdministrativa = $areaAdministrativa;

        return $this;
    }
}
