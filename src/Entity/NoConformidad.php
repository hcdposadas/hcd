<?php

namespace App\Entity;

use App\Repository\NoConformidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=NoConformidadRepository::class)
 * @Vich\Uploadable
 */
class NoConformidad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AreaAdministrativa::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity=Persona::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $empleado;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $origen;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $categoria;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $requisito;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripcionHallazgo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @Vich\UploadableField(mapping="no_conformidad_images", fileNameProperty="imagen")
     * @var File
     */
    private $imagenFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $documento;

    /**
     * @Vich\UploadableField(mapping="no_conformidad_docs", fileNameProperty="documento")
     * @var File
     */
    private $documentoFile;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $estado = 'nuevo';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable_calidad;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $asignadoA;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $creadoPor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $correccion;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaCorreccion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $analisisCausa;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $accionCorrectiva;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaAccionCorrectiva;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $correccionVerificada;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $efectividadVerificada;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comentariosVerificacion;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $analisisAceptado;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $explicacionRevisionAnalisis;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $explicacionDesestimado;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $nuevaFechaCorreccion;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $norma;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $requisitoEspecifico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pgcd;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $requisitoLegal;

    /**
     * @ORM\OneToMany(targetEntity=NoConformidadAdjunto::class, mappedBy="noConformidad", cascade={"persist", "remove"})
     */
    private $adjuntos;

    public function __construct()
    {
        $this->adjuntos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmpleado(): ?Persona
    {
        return $this->empleado;
    }

    public function setEmpleado(?Persona $empleado): self
    {
        $this->empleado = $empleado;
        return $this;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(string $origen): self
    {
        $this->origen = $origen;
        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getRequisito(): ?string
    {
        return $this->requisito;
    }

    public function setRequisito(?string $requisito): self
    {
        $this->requisito = $requisito;
        return $this;
    }

    public function getDescripcionHallazgo(): ?string
    {
        return $this->descripcionHallazgo;
    }

    public function setDescripcionHallazgo(?string $descripcionHallazgo): self
    {
        $this->descripcionHallazgo = $descripcionHallazgo;
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

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function setImagenFile(?File $imagen = null): void
    {
        $this->imagenFile = $imagen;
        if ($imagen) {
            $this->fecha = new \DateTime();
        }
    }

    public function getImagenFile(): ?File
    {
        return $this->imagenFile;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): self
    {
        $this->documento = $documento;
        return $this;
    }

    public function setDocumentoFile(?File $documento = null): void
    {
        $this->documentoFile = $documento;
        if ($documento) {
            $this->fecha = new \DateTime();
        }
    }

    public function getDocumentoFile(): ?File
    {
        return $this->documentoFile;
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

    public function getResponsableCalidad(): ?string
    {
        return $this->responsable_calidad;
    }

    public function setResponsableCalidad(?string $responsable_calidad): self
    {
        $this->responsable_calidad = $responsable_calidad;
        return $this;
    }

    public function getAsignadoA(): ?Usuario
    {
        return $this->asignadoA;
    }

    public function setAsignadoA(?Usuario $asignadoA): self
    {
        $this->asignadoA = $asignadoA;
        return $this;
    }

    public function getCorreccion(): ?string
    {
        return $this->correccion;
    }

    public function setCorreccion(?string $correccion): self
    {
        $this->correccion = $correccion;
        return $this;
    }

    public function getFechaCorreccion(): ?\DateTimeInterface
    {
        return $this->fechaCorreccion;
    }

    public function setFechaCorreccion(?\DateTimeInterface $fechaCorreccion): self
    {
        $this->fechaCorreccion = $fechaCorreccion;
        return $this;
    }

    public function getAnalisisCausa(): ?string
    {
        return $this->analisisCausa;
    }

    public function setAnalisisCausa(?string $analisisCausa): self
    {
        $this->analisisCausa = $analisisCausa;
        return $this;
    }

    public function getAccionCorrectiva(): ?string
    {
        return $this->accionCorrectiva;
    }

    public function setAccionCorrectiva(?string $accionCorrectiva): self
    {
        $this->accionCorrectiva = $accionCorrectiva;
        return $this;
    }

    public function getFechaAccionCorrectiva(): ?\DateTimeInterface
    {
        return $this->fechaAccionCorrectiva;
    }

    public function setFechaAccionCorrectiva(?\DateTimeInterface $fechaAccionCorrectiva): self
    {
        $this->fechaAccionCorrectiva = $fechaAccionCorrectiva;
        return $this;
    }

    public function getCorreccionVerificada(): ?bool
    {
        return $this->correccionVerificada;
    }

    public function setCorreccionVerificada(?bool $correccionVerificada): self
    {
        $this->correccionVerificada = $correccionVerificada;
        return $this;
    }

    public function getEfectividadVerificada(): ?bool
    {
        return $this->efectividadVerificada;
    }

    public function setEfectividadVerificada(?bool $efectividadVerificada): self
    {
        $this->efectividadVerificada = $efectividadVerificada;
        return $this;
    }

    public function getComentariosVerificacion(): ?string
    {
        return $this->comentariosVerificacion;
    }

    public function setComentariosVerificacion(?string $comentariosVerificacion): self
    {
        $this->comentariosVerificacion = $comentariosVerificacion;
        return $this;
    }

    public function getAnalisisAceptado(): ?bool
    {
        return $this->analisisAceptado;
    }

    public function setAnalisisAceptado(?bool $analisisAceptado): self
    {
        $this->analisisAceptado = $analisisAceptado;
        return $this;
    }

    public function getExplicacionRevisionAnalisis(): ?string
    {
        return $this->explicacionRevisionAnalisis;
    }

    public function setExplicacionRevisionAnalisis(?string $explicacionRevisionAnalisis): self
    {
        $this->explicacionRevisionAnalisis = $explicacionRevisionAnalisis;
        return $this;
    }

    public function getExplicacionDesestimado(): ?string
    {
        return $this->explicacionDesestimado;
    }

    public function setExplicacionDesestimado(?string $explicacionDesestimado): self
    {
        $this->explicacionDesestimado = $explicacionDesestimado;
        return $this;
    }

    public function getNuevaFechaCorreccion(): ?\DateTimeInterface
    {
        return $this->nuevaFechaCorreccion;
    }

    public function setNuevaFechaCorreccion(?\DateTimeInterface $nuevaFechaCorreccion): self
    {
        $this->nuevaFechaCorreccion = $nuevaFechaCorreccion;
        return $this;
    }

    public function getNorma(): ?string
    {
        return $this->norma;
    }

    public function setNorma(?string $norma): self
    {
        $this->norma = $norma;
        return $this;
    }

    public function getRequisitoEspecifico(): ?string
    {
        return $this->requisitoEspecifico;
    }

    public function setRequisitoEspecifico(?string $requisitoEspecifico): self
    {
        $this->requisitoEspecifico = $requisitoEspecifico;
        return $this;
    }

    public function getPgcd(): ?string
    {
        return $this->pgcd;
    }

    public function setPgcd(?string $pgcd): self
    {
        $this->pgcd = $pgcd;
        return $this;
    }

    public function getRequisitoLegal(): ?bool
    {
        return $this->requisitoLegal;
    }

    public function setRequisitoLegal(?bool $requisitoLegal): self
    {
        $this->requisitoLegal = $requisitoLegal;
        return $this;
    }

    /**
     * @return Collection|NoConformidadAdjunto[]
     */
    public function getAdjuntos(): Collection
    {
        return $this->adjuntos;
    }

    public function addAdjunto(NoConformidadAdjunto $adjunto): self
    {
        if (!$this->adjuntos->contains($adjunto)) {
            $this->adjuntos[] = $adjunto;
            $adjunto->setNoConformidad($this);
        }

        return $this;
    }

    public function removeAdjunto(NoConformidadAdjunto $adjunto): self
    {
        if ($this->adjuntos->removeElement($adjunto)) {
            // set the owning side to null (unless already changed)
            if ($adjunto->getNoConformidad() === $this) {
                $adjunto->setNoConformidad(null);
            }
        }

        return $this;
    }

    public function getCreadoPor(): ?Usuario
    {
        return $this->creadoPor;
    }

    public function setCreadoPor(?Usuario $creadoPor): self
    {
        $this->creadoPor = $creadoPor;
        return $this;
    }
} 