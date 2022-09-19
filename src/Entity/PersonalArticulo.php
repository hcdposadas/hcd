<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\PersonalArticuloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalArticuloRepository::class)
 */
class PersonalArticulo extends BaseClass {
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
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=OrdenMedica::class, mappedBy="articulo")
     */
    private $ordenMedicas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dias;

    public function __construct()
    {
        $this->ordenMedicas = new ArrayCollection();
    }

	public function __toString(): ?string {
                                 		return $this->numero;
                                 	}

	public function tituloLargo(): ?string {
                                 		return $this->numero . ' - ' . $this->descripcion;
                                 	}

	public function getId(): ?int {
                                 		return $this->id;
                                 	}

	public function getNumero(): ?string {
                                 		return $this->numero;
                                 	}

	public function setNumero( string $numero ): self {
                                 		$this->numero = $numero;
                                 
                                 		return $this;
                                 	}

	public function getDescripcion(): ?string {
                                 		return $this->descripcion;
                                 	}

	public function setDescripcion( ?string $descripcion ): self {
                                 		$this->descripcion = $descripcion;
                                 
                                 		return $this;
                                 	}

    /**
     * @return Collection|OrdenMedica[]
     */
    public function getOrdenMedicas(): Collection
    {
        return $this->ordenMedicas;
    }

    public function addOrdenMedica(OrdenMedica $ordenMedica): self
    {
        if (!$this->ordenMedicas->contains($ordenMedica)) {
            $this->ordenMedicas[] = $ordenMedica;
            $ordenMedica->setArticulo($this);
        }

        return $this;
    }

    public function removeOrdenMedica(OrdenMedica $ordenMedica): self
    {
        if ($this->ordenMedicas->contains($ordenMedica)) {
            $this->ordenMedicas->removeElement($ordenMedica);
            // set the owning side to null (unless already changed)
            if ($ordenMedica->getArticulo() === $this) {
                $ordenMedica->setArticulo(null);
            }
        }

        return $this;
    }

    public function getDias(): ?int
    {
        return $this->dias;
    }

    public function setDias(?int $dias): self
    {
        $this->dias = $dias;

        return $this;
    }
}
