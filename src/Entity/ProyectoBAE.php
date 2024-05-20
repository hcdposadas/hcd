<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Expediente;
use App\Entity\Giro;
use App\Entity\Base\BaseClass;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * ProyectoBAE
 *
 * @ORM\Table(name="proyecto_b_a_e")
 * @ORM\Entity(repositoryClass="App\Repository\ProyectoBAERepository")
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"expediente", "boletinAsuntoEntrado"},
 *     errorPath="expediente",
 *     message="Este expediente ya existe en el BAE"
 * )
 */
class ProyectoBAE extends BaseClass {
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;


	/**
	 * @var Expediente $expediente
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Expediente")
	 * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
	 */
	private $expediente;

	/**
	 * @var BoletinAsuntoEntrado $boletinAsuntoEntrado
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\BoletinAsuntoEntrado", inversedBy="proyectos")
	 * @ORM\JoinColumn(name="boletin_asunto_entrado_id", referencedColumnName="id", nullable=true)
	 */
	private $boletinAsuntoEntrado;

	/**
	 * @var $informeDem
	 *
	 * @ORM\Column(name="es_informe_dem", type="boolean", nullable=true)
	 */
	private $esInformeDem;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="extracto", type="text", nullable=true)
	 */
	private $extracto;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Giro", mappedBy="proyectoBae", cascade={"persist", "remove"})
     *
     */
    private $giros;

	/**
	 * @var $incorporadoEnSesion
	 *
	 * @ORM\Column(name="incorporado_en_sesion", type="boolean", nullable=true)
	 */
	private $incorporadoEnSesion;

	/**
	 * @var $tratamientoSobretabla
	 *
	 * @ORM\Column(name="tratamiento_sobretabla", type="boolean", nullable=true)
	 */
	private $tratamientoSobretabla;

	/**
     * @Vich\UploadableField(mapping="firmado", fileNameProperty="firmado")
     * @var File
     */
    private $firmadoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firmado;

    /**
     * @Vich\UploadableField(mapping="digesto", fileNameProperty="digesto")
     * @var File
     */
    private $digestoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $digesto;

	/**
     * @Vich\UploadableField(mapping="pedido", fileNameProperty="pedido")
     * @var File
     */
    private $pedidoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pedido;


	/**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return ProyectoBAE
     */
    public function setFirmadoFile(File $file = null)
    {
        $this->firmadoFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFirmadoFile()
    {
        return $this->firmadoFile;
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
     * @return ProyectoBAE
     */
    public function setPedidoFile(File $file = null)
    {
        $this->pedidoFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPedidoFile()
    {
        return $this->pedidoFile;
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
     * @return ProyectoBAE
     */
    public function setDigestoFile(File $file = null)
    {
        $this->digestoFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
//			$this->updatedAt = new \DateTimeImmutable();
            $this->fechaActualizacion = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getDigestoFile()
    {
        return $this->digestoFile;
    }

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
                           		return $this->id;
                           	}

	/**
	 * Set fechaCreacion
	 *
	 * @param \DateTime $fechaCreacion
	 *
	 * @return ProyectoBAE
	 */
	public function setFechaCreacion( $fechaCreacion ) {
                           		$this->fechaCreacion = $fechaCreacion;
                           
                           		return $this;
                           	}

	/**
	 * Set fechaActualizacion
	 *
	 * @param \DateTime $fechaActualizacion
	 *
	 * @return ProyectoBAE
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
                           		$this->fechaActualizacion = $fechaActualizacion;
                           
                           		return $this;
                           	}

	/**
	 * Set expediente
	 *
	 * @param \App\Entity\Expediente $expediente
	 *
	 * @return ProyectoBAE
	 */
	public function setExpediente( \App\Entity\Expediente $expediente = null ) {
                           		$this->expediente = $expediente;
                           
                           		return $this;
                           	}

	/**
	 * Get expediente
	 *
	 * @return \App\Entity\Expediente
	 */
	public function getExpediente() {
                           		return $this->expediente;
                           	}

	/**
	 * Set boletinAsuntoEntrado
	 *
	 * @param \App\Entity\BoletinAsuntoEntrado $boletinAsuntoEntrado
	 *
	 * @return ProyectoBAE
	 */
	public function setBoletinAsuntoEntrado( \App\Entity\BoletinAsuntoEntrado $boletinAsuntoEntrado = null ) {
                           		$this->boletinAsuntoEntrado = $boletinAsuntoEntrado;
                           
                           		return $this;
                           	}

	/**
	 * Get boletinAsuntoEntrado
	 *
	 * @return \App\Entity\BoletinAsuntoEntrado
	 */
	public function getBoletinAsuntoEntrado() {
                           		return $this->boletinAsuntoEntrado;
                           	}

	/**
	 * Set creadoPor
	 *
	 * @param \App\Entity\Usuario $creadoPor
	 *
	 * @return ProyectoBAE
	 */
	public function setCreadoPor( \App\Entity\Usuario $creadoPor = null ) {
                           		$this->creadoPor = $creadoPor;
                           
                           		return $this;
                           	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \App\Entity\Usuario $actualizadoPor
	 *
	 * @return ProyectoBAE
	 */
	public function setActualizadoPor( \App\Entity\Usuario $actualizadoPor = null ) {
                           		$this->actualizadoPor = $actualizadoPor;
                           
                           		return $this;
                           	}

    /**
     * Set esInformeDem
     *
     * @param boolean $esInformeDem
     *
     * @return ProyectoBAE
     */
    public function setEsInformeDem($esInformeDem)
    {
        $this->esInformeDem = $esInformeDem;

        return $this;
    }

    /**
     * Get esInformeDem
     *
     * @return boolean
     */
    public function getEsInformeDem()
    {
        return $this->esInformeDem;
    }

    /**
     * Set extracto
     *
     * @param string $extracto
     *
     * @return ProyectoBAE
     */
    public function setExtracto($extracto)
    {
        $this->extracto = $extracto;

        return $this;
    }

    /**
     * Get extracto
     *
     * @return string
     */
    public function getExtracto()
    {
        return $this->extracto;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->giros = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add giro
     *
     * @param \App\Entity\Giro $giro
     *
     * @return ProyectoBAE
     */
    public function addGiro(\App\Entity\Giro $giro)
    {
        $giro->setProyectoBae( $this );

        $this->giros->add( $giro );

        return $this;
    }

    /**
     * Remove giro
     *
     * @param \App\Entity\Giro $giro
     */
    public function removeGiro(\App\Entity\Giro $giro)
    {
        $this->giros->removeElement($giro);
    }

    /**
     * Get giros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGiros()
    {
        return $this->giros;
    }

    public function getGirosOrdenados()
    {
        $iterator = $this->getGiros()->getIterator();

        $iterator->uasort(function (Giro $a, Giro $b) {
            if ($a->getCabecera()) {
                return -1;
            } elseif ($b->getCabecera()) {
                return 1;
            } else {
                return ($a->getOrden() < $b->getOrden()) ? -1 : 1;
            }
        });

        return new \Doctrine\Common\Collections\ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * Set incorporadoEnSesion
     *
     * @param boolean $incorporadoEnSesion
     *
     * @return ProyectoBAE
     */
    public function setIncorporadoEnSesion($incorporadoEnSesion)
    {
        $this->incorporadoEnSesion = $incorporadoEnSesion;

        return $this;
    }

    /**
     * Get incorporadoEnSesion
     *
     * @return boolean
     */
    public function getIncorporadoEnSesion()
    {
        return $this->incorporadoEnSesion;
    }

	/**
	 * @return mixed
	 */
	public function getTratamientoSobretabla() {
                           		return $this->tratamientoSobretabla;
                           	}

	/**
	 * @param mixed $tratamientoSobretabla
	 */
	public function setTratamientoSobretabla( $tratamientoSobretabla ) {
                           		$this->tratamientoSobretabla = $tratamientoSobretabla;
                           	}

    /**
	 * @return File|null
	 */
    public function getFirmado(): ?string
    {
        return $this->firmado;
    }

    /**
	 * @param string $firmado
	 *
	 * @return ProyectoBAE
	 */
    public function setFirmado(?string $firmado): self
    {
        $this->firmado = $firmado;

        return $this;
    }

    public function getDigesto(): ?string
    {
        return $this->digesto;
    }

    public function setDigesto(?string $digesto): self
    {
        $this->digesto = $digesto;

        return $this;
    }

    public function getPedido(): ?string
    {
        return $this->pedido;
    }

    public function setPedido(?string $pedido): self
    {
        $this->pedido = $pedido;

        return $this;
    }
}
