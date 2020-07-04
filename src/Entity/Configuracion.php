<?php

namespace App\Entity;

use App\Entity\Base\BaseClass;
use App\Repository\ConfiguracionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=ConfiguracionRepository::class)
 */
class Configuracion extends BaseClass {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $appleTouchIcon;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $encabezadoTextoDefinitivo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $escudo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $logo16;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $logo32;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $logo129;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $logo269;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $logotipo;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $selloPresidencia;

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="appleTouchIcon")
	 * @var File
	 */
	private $appleTouchIconFile;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setAppleTouchIconFile( File $file = null ) {
		$this->appleTouchIconFile = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getAppleTouchIconFile() {
		return $this->appleTouchIconFile;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="encabezadoTextoDefinitivo")
	 * @var File
	 */
	private $encabezadoTextoDefinitivoFile;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setEncabezadoTextoDefinitivoFile( File $file = null ) {
		$this->encabezadoTextoDefinitivoFile = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getEncabezadoTextoDefinitivoFile() {
		return $this->encabezadoTextoDefinitivoFile;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="escudo")
	 * @var File
	 */
	private $escudoFile;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setEscudoFile( File $file = null ) {
		$this->escudoFile = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getEscudoFile() {
		return $this->escudoFile;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="logo16")
	 * @var File
	 */
	private $logo16File;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setLogo16File( File $file = null ) {
		$this->logo16File = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getLogo16File() {
		return $this->logo16File;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="logo32")
	 * @var File
	 */
	private $logo32File;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setLogo32File( File $file = null ) {
		$this->logo32File = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getLogo32File() {
		return $this->logo16File;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="logo129")
	 * @var File
	 */
	private $logo129File;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setLogo129File( File $file = null ) {
		$this->logo129File = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getLogo129File() {
		return $this->logo129File;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="logo269")
	 * @var File
	 */
	private $logo269File;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setLogo269File( File $file = null ) {
		$this->logo269File = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getLogo269File() {
		return $this->logo269File;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="logotipo")
	 * @var File
	 */
	private $logotipoFile;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setLogotipoFile( File $file = null ) {
		$this->logotipoFile = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getLogotipoFile() {
		return $this->logotipoFile;
	}

	/**
	 * @Vich\UploadableField(mapping="sis_images", fileNameProperty="selloPresidencia")
	 * @var File
	 */
	private $selloPresidenciaFile;

	/**
	 *
	 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
	 *
	 * @return Configuracion
	 */
	public function setSelloPresidenciaFile( File $file = null ) {
		$this->selloPresidenciaFile = $file;

		if ( $file ) {
			$this->fechaActualizacion = new \DateTime( 'now' );
		}

		return $this;
	}

	/**
	 * @return File|null
	 */
	public function getSelloPresidenciaFile() {
		return $this->selloPresidenciaFile;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getAppleTouchIcon(): ?string {
		return $this->appleTouchIcon;
	}

	public function setAppleTouchIcon( ?string $appleTouchIcon ): self {
		$this->appleTouchIcon = $appleTouchIcon;

		return $this;
	}

	public function getEncabezadoTextoDefinitivo(): ?string {
		return $this->encabezadoTextoDefinitivo;
	}

	public function setEncabezadoTextoDefinitivo( ?string $encabezadoTextoDefinitivo ): self {
		$this->encabezadoTextoDefinitivo = $encabezadoTextoDefinitivo;

		return $this;
	}

	public function getEscudo(): ?string {
		return $this->escudo;
	}

	public function setEscudo( ?string $escudo ): self {
		$this->escudo = $escudo;

		return $this;
	}

	public function getLogo16(): ?string {
		return $this->logo16;
	}

	public function setLogo16( ?string $logo16 ): self {
		$this->logo16 = $logo16;

		return $this;
	}

	public function getLogo32(): ?string {
		return $this->logo32;
	}

	public function setLogo32( ?string $logo32 ): self {
		$this->logo32 = $logo32;

		return $this;
	}

	public function getLogo129(): ?string {
		return $this->logo129;
	}

	public function setLogo129( ?string $logo129 ): self {
		$this->logo129 = $logo129;

		return $this;
	}

	public function getLogo269(): ?string {
		return $this->logo269;
	}

	public function setLogo269( ?string $logo269 ): self {
		$this->logo269 = $logo269;

		return $this;
	}

	public function getLogotipo(): ?string {
		return $this->logotipo;
	}

	public function setLogotipo( ?string $logotipo ): self {
		$this->logotipo = $logotipo;

		return $this;
	}

	public function getSelloPresidencia(): ?string {
		return $this->selloPresidencia;
	}

	public function setSelloPresidencia( ?string $selloPresidencia ): self {
		$this->selloPresidencia = $selloPresidencia;

		return $this;
	}


}
