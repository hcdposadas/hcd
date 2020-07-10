<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalDDJJPersonaACargoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalDDJJPersonaACargoRepository::class)
 */
class PersonalDDJJPersonaACargo extends BaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalDeclaracionJurada::class, inversedBy="personalDDJJPersonaACargos", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $ddjj;

    /**
     * @ORM\ManyToOne(targetEntity=PersonaACargo::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $personasACargo;

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

    public function getPersonasACargo(): ?PersonaACargo
    {
        return $this->personasACargo;
    }

    public function setPersonasACargo(?PersonaACargo $personasACargo): self
    {
        $this->personasACargo = $personasACargo;

        return $this;
    }
}
