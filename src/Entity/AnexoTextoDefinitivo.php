<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Base\BaseClass;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AnexoTextoDefinitivo
 *
 * @ORM\Table(name="anexo_texto_definitivo")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\AnexoTextoDefinitivoRepository")
 */
class AnexoTextoDefinitivo extends BaseClass {
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
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
	 */
	private $descripcion;


	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\TextoDefinitivo", inversedBy="anexos")
	 * @ORM\JoinColumn(name="texto_definitivo_id", referencedColumnName="id")
	 */
	private $textoDefinitivo;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	private $anexo;


	/**
	 * @Vich\UploadableField(mapping="expedientes_anexos", fileNameProperty="anexo")
	 * @var File
	 * @Assert\Image(mimeTypes={ "image/*" })
	 */
	private $anexoFile;

	/**
	 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
	 * of 'UploadedFile' is injected into this setter to trigger the  update. If this
	 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
	 * must be able to accept an instance of 'File' as the bundle will inject one here
	 * during Doctrine hydration.
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
	 *
	 * @return AnexoTextoDefinitivo
	 */
	public function setAnexoFile( File $file = null ) {
		$this->anexoFile = $file;

		if ( $file ) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
			$this->fechaActualizacion =  new \DateTime( 'now' ) ;
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getAnexoFile() {
		return $this->anexoFile;
	}


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return AnexoTextoDefinitivo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set anexo
     *
     * @param string $anexo
     *
     * @return AnexoTextoDefinitivo
     */
    public function setAnexo($anexo)
    {
        $this->anexo = $anexo;

        return $this;
    }

    /**
     * Get anexo
     *
     * @return string
     */
    public function getAnexo()
    {
        return $this->anexo;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return AnexoTextoDefinitivo
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
     * @return AnexoTextoDefinitivo
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set textoDefinitivo
     *
     * @param \App\Entity\TextoDefinitivo $textoDefinitivo
     *
     * @return AnexoTextoDefinitivo
     */
    public function setTextoDefinitivo(\App\Entity\TextoDefinitivo $textoDefinitivo = null)
    {
        $this->textoDefinitivo = $textoDefinitivo;

        return $this;
    }

    /**
     * Get textoDefinitivo
     *
     * @return \App\Entity\TextoDefinitivo
     */
    public function getTextoDefinitivo()
    {
        return $this->textoDefinitivo;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return AnexoTextoDefinitivo
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
     * @return AnexoTextoDefinitivo
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
