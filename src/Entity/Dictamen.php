<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Dictamen
 *
 * @Vich\Uploadable
 * @ORM\Table(name="dictamen")
 * @ORM\Entity(repositoryClass="App\Repository\DictamenRepository")
 */
class Dictamen extends BaseClass
{
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
     * @ORM\Column(name="texto_dictamen", type="text", nullable=true)
     */
    private $textoDictamen;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Expediente", inversedBy="dictamenes", cascade={"persist"})
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
     */
    private $expediente;

    /**
     * @Vich\UploadableField(mapping="dictamen", fileNameProperty="dictamen")
     * @var File
     */
    private $dictamenFile;
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CargoPersona")
     * @ORM\JoinColumn(name="presidente_comision_id", referencedColumnName="id", nullable=true)
     */
    private $presidenteComision;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoProyecto")
     * @ORM\JoinColumn(name="tipo_proyecto_id", referencedColumnName="id")
     */
    private $tipoProyecto;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PeriodoLegislativo")
     * @ORM\JoinColumn(name="periodo_legislativo_id", referencedColumnName="id")
     */
    private $periodoLegislativo;


    /**
     * @var IniciadorExpediente[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FirmanteDictamen", mappedBy="dictamen", cascade={"persist"})
     *
     */
    private $firmantes;

    /**
     * @var \DateTime $fecha
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\Entity\AnexoDictamen", mappedBy="dictamen", cascade={"persist", "remove"})
     *
     */
    private $anexos;

	/**
	 * @var TextoDefinitivo[]
	 *
	 * @ORM\OneToMany(targetEntity="App\Entity\TextoDefinitivo", mappedBy="dictamen")
	 *
	 */
	private $textosDefinitivos;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getExpediente()->__toString() . ' - '
            . $this->getTipoProyecto()->getNombre() . ' - '
            . $this->getFecha()->format('d/m/Y');
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
     * @return Dictamen
     */
    public function setDictamenFile(File $file = null)
    {
        $this->dictamenFile = $file;

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
    public function getDictamenFile()
    {
        return $this->dictamenFile;
    }

    /**
     * @param string $dictamen
     *
     * @return Expediente
     */
    public function setDictamen($dictamen)
    {
        $this->dictamen = $dictamen;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDictamen()
    {
        return $this->dictamen;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Dictamen
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
     *
     * @return Dictamen
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
     *
     * @return Dictamen
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
     *
     * @return Dictamen
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Set textoDictamen
     *
     * @param string $textoDictamen
     *
     * @return Dictamen
     */
    public function setTextoDictamen($textoDictamen)
    {
        $this->textoDictamen = $textoDictamen;

        return $this;
    }

    /**
     * Get textoDictamen
     *
     * @return string
     */
    public function getTextoDictamen()
    {
        return $this->textoDictamen;
    }

    /**
     * Set expediente
     *
     * @param \App\Entity\Expediente $expediente
     *
     * @return Dictamen
     */
    public function setExpediente(\App\Entity\Expediente $expediente = null)
    {
        $this->expediente = $expediente;

        return $this;
    }

    /**
     * Get expediente
     *
     * @return \App\Entity\Expediente
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Set presidenteComision
     *
     * @param \App\Entity\CargoPersona $presidenteComision
     *
     * @return Dictamen
     */
    public function setPresidenteComision(\App\Entity\CargoPersona $presidenteComision = null)
    {
        $this->presidenteComision = $presidenteComision;

        return $this;
    }

    /**
     * Get presidenteComision
     *
     * @return \App\Entity\CargoPersona
     */
    public function getPresidenteComision()
    {
        return $this->presidenteComision;
    }

    /**
     * Set tipoProyecto
     *
     * @param \App\Entity\TipoProyecto $tipoProyecto
     *
     * @return Dictamen
     */
    public function setTipoProyecto(\App\Entity\TipoProyecto $tipoProyecto = null)
    {
        $this->tipoProyecto = $tipoProyecto;

        return $this;
    }

    /**
     * Get tipoProyecto
     *
     * @return \App\Entity\TipoProyecto
     */
    public function getTipoProyecto()
    {
        return $this->tipoProyecto;
    }

    /**
     * Set periodoLegislativo
     *
     * @param \App\Entity\PeriodoLegislativo $periodoLegislativo
     *
     * @return Dictamen
     */
    public function setPeriodoLegislativo(\App\Entity\PeriodoLegislativo $periodoLegislativo = null)
    {
        $this->periodoLegislativo = $periodoLegislativo;

        return $this;
    }

    /**
     * Get periodoLegislativo
     *
     * @return \App\Entity\PeriodoLegislativo
     */
    public function getPeriodoLegislativo()
    {
        return $this->periodoLegislativo;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->firmantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->anexos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add firmante
     *
     * @param \App\Entity\FirmanteDictamen $firmante
     *
     * @return Dictamen
     */
    public function addFirmante(\App\Entity\FirmanteDictamen $firmante)
    {

        $firmante->setDictamen($this);

        $this->firmantes->add($firmante);

        return $this;
    }

    /**
     * Remove firmante
     *
     * @param \App\Entity\FirmanteDictamen $firmante
     */
    public function removeFirmante(\App\Entity\FirmanteDictamen $firmante)
    {
        $this->firmantes->removeElement($firmante);
    }

    /**
     * Get firmantes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFirmantes()
    {
        return $this->firmantes;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Dictamen
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Add anexo
     *
     * @param \App\Entity\AnexoDictamen $anexo
     *
     * @return Dictamen
     */
    public function addAnexo(\App\Entity\AnexoDictamen $anexo)
    {
        $anexo->setDictamen($this);

        $this->anexos->add($anexo);

        return $this;
    }

    /**
     * Remove anexo
     *
     * @param \App\Entity\AnexoDictamen $anexo
     */
    public function removeAnexo(\App\Entity\AnexoDictamen $anexo)
    {
        $this->anexos->removeElement($anexo);
    }

    /**
     * Get anexos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnexos()
    {
        return $this->anexos;
    }
}
