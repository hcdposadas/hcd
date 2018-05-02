<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use UtilBundle\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Dictamen
 *
 * @Vich\Uploadable
 * @ORM\Table(name="dictamen")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\DictamenRepository")
 */
class Dictamen extends BaseClass {
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
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente", inversedBy="dictamenes")
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
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CargoPersona")
	 * @ORM\JoinColumn(name="presidente_comision_id", referencedColumnName="id", nullable=true)
	 */
	private $presidenteComision;

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
	public function setDictamenFile( File $file = null ) {
		$this->dictamenFile = $file;

		if ( $file ) {
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
	public function getDictamenFile() {
		return $this->dictamenFile;
	}

	/**
	 * @param string $dictamen
	 *
	 * @return Expediente
	 */
	public function setDictamen( $dictamen ) {
		$this->dictamen = $dictamen;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDictamen() {
		return $this->dictamen;
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
	 * @return Dictamen
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
	 * @return Dictamen
	 */
	public function setFechaActualizacion( $fechaActualizacion ) {
		$this->fechaActualizacion = $fechaActualizacion;

		return $this;
	}

	/**
	 * Set creadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $creadoPor
	 *
	 * @return Dictamen
	 */
	public function setCreadoPor( \UsuariosBundle\Entity\Usuario $creadoPor = null ) {
		$this->creadoPor = $creadoPor;

		return $this;
	}

	/**
	 * Set actualizadoPor
	 *
	 * @param \UsuariosBundle\Entity\Usuario $actualizadoPor
	 *
	 * @return Dictamen
	 */
	public function setActualizadoPor( \UsuariosBundle\Entity\Usuario $actualizadoPor = null ) {
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
	public function setTextoDictamen( $textoDictamen ) {
		$this->textoDictamen = $textoDictamen;

		return $this;
	}

	/**
	 * Get textoDictamen
	 *
	 * @return string
	 */
	public function getTextoDictamen() {
		return $this->textoDictamen;
	}

	/**
	 * Set expediente
	 *
	 * @param \MesaEntradaBundle\Entity\Expediente $expediente
	 *
	 * @return Dictamen
	 */
	public function setExpediente( \MesaEntradaBundle\Entity\Expediente $expediente = null ) {
		$this->expediente = $expediente;

		return $this;
	}

	/**
	 * Get expediente
	 *
	 * @return \MesaEntradaBundle\Entity\Expediente
	 */
	public function getExpediente() {
		return $this->expediente;
	}

    /**
     * Set presidenteComision
     *
     * @param \AppBundle\Entity\CargoPersona $presidenteComision
     *
     * @return Dictamen
     */
    public function setPresidenteComision(\AppBundle\Entity\CargoPersona $presidenteComision = null)
    {
        $this->presidenteComision = $presidenteComision;

        return $this;
    }

    /**
     * Get presidenteComision
     *
     * @return \AppBundle\Entity\CargoPersona
     */
    public function getPresidenteComision()
    {
        return $this->presidenteComision;
    }
}
