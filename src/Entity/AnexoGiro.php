<?php

namespace App\Entity;

use App\Repository\AnexoGiroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * @ORM\Entity(repositoryClass=AnexoGiroRepository::class)
 * @Vich\Uploadable
 */
class AnexoGiro
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
    private $anexo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\ManyToOne(targetEntity=GiroAdministrativo::class, inversedBy="anexoGiros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $giro;

    	/**
	 * @Vich\UploadableField(mapping="giros_anexos", fileNameProperty="anexo")
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

    public function getAnexo(): ?string
    {
        return $this->anexo;
    }

    public function setAnexo(string $anexo): self
    {
        $this->anexo = $anexo;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getGiro(): ?GiroAdministrativo
    {
        return $this->giro;
    }

    public function setGiro(?GiroAdministrativo $giro): self
    {
        $this->giro = $giro;

        return $this;
    }
}
