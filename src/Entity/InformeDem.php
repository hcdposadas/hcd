<?php

namespace App\Entity;

use App\Repository\InformeDemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=InformeDemRepository::class)
 *  * @Vich\Uploadable
 */
class InformeDem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archivo;

    /**
     * @ORM\ManyToOne(targetEntity=Expediente::class, inversedBy="informeDems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expediente;

    	/**
	 * @Vich\UploadableField(mapping="expedientes_dem", fileNameProperty="archivo")
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
	 * @return AnexoExpediente
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExpediente(): ?Expediente
    {
        return $this->expediente;
    }

    public function setExpediente(?Expediente $expediente): self
    {
        $this->expediente = $expediente;

        return $this;
    }
}
