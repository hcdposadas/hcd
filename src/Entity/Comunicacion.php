<?php

namespace App\Entity;

use App\Repository\ComunicacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=ComunicacionRepository::class)
 * @Vich\Uploadable
 */
class Comunicacion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    private $anio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class, inversedBy="comunicacions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $areaOrigen;

    /**
     * @ORM\ManyToMany(targetEntity=AreaAdministrativa::class, inversedBy="comunicacionsR")
     */
    private $areaDestino;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $archivo;

    	/**
	 * @Vich\UploadableField(mapping="comunicados", fileNameProperty="archivo")
	 * @var File
	 */
	private $archivoFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity=RecibidoComunicado::class, mappedBy="comunicacion")
     */
    private $recibidoComunicados;

    public function __construct()
    {
        $this->areaDestino = new ArrayCollection();
        $this->recibidoComunicados = new ArrayCollection();
    }

    /**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return Comunicacion
	 */
	public function setArchivoFile(File $file = null)
	{
		$this->archivoFile = $file;

		if ($file) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
			//			$this->updatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getArchivoFile()
	{
		return $this->archivoFile;
	}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function setAnio(int $anio): self
    {
        $this->anio = $anio;

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

    public function getAreaOrigen(): ?AreaAdministrativa
    {
        return $this->areaOrigen;
    }

    public function setAreaOrigen(?AreaAdministrativa $areaOrigen): self
    {
        $this->areaOrigen = $areaOrigen;

        return $this;
    }

    /**
     * @return Collection|AreaAdministrativa[]
     */
    public function getAreaDestino(): Collection
    {
        return $this->areaDestino;
    }

    public function addAreaDestino(AreaAdministrativa $areaDestino): self
    {
        if (!$this->areaDestino->contains($areaDestino)) {
            $this->areaDestino[] = $areaDestino;
        }

        return $this;
    }

    public function removeAreaDestino(AreaAdministrativa $areaDestino): self
    {
        if ($this->areaDestino->contains($areaDestino)) {
            $this->areaDestino->removeElement($areaDestino);
        }

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

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

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
            $recibidoComunicado->setComunicacion($this);
        }

        return $this;
    }

    public function removeRecibidoComunicado(RecibidoComunicado $recibidoComunicado): self
    {
        if ($this->recibidoComunicados->contains($recibidoComunicado)) {
            $this->recibidoComunicados->removeElement($recibidoComunicado);
            // set the owning side to null (unless already changed)
            if ($recibidoComunicado->getComunicacion() === $this) {
                $recibidoComunicado->setComunicacion(null);
            }
        }

        return $this;
    }
}
