<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalDDJJConyugeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalDDJJConyugeRepository::class)
 */
class PersonalDDJJConyuge extends BaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalDeclaracionJurada::class, inversedBy="personalDDJJConyuges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ddjj;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalConyuge::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $conyuge;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDdjj(): ?PersonalDeclaracionJurada
    {
        return $this->ddjj;
    }

    public function setDdjj(?PersonalDeclaracionJurada $ddjj): self
    {
        $this->ddjj = $ddjj;

        return $this;
    }

    public function getConyuge(): ?PersonalConyuge
    {
        return $this->conyuge;
    }

    public function setConyuge(?PersonalConyuge $conyuge): self
    {
        $this->conyuge = $conyuge;

        return $this;
    }
}
