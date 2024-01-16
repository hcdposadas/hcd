<?php

namespace App\Entity;

use App\Repository\ExpedienteBloqueadoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpedienteBloqueadoRepository::class)
 */
class ExpedienteBloqueado
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
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $letra;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ano;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getLetra(): ?string
    {
        return $this->letra;
    }

    public function setLetra(string $letra): self
    {
        $this->letra = $letra;

        return $this;
    }

    public function getAno(): ?string
    {
        return $this->ano;
    }

    public function setAno(?string $ano): self
    {
        $this->ano = $ano;

        return $this;
    }
}
