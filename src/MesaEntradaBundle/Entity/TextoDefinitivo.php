<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * TextoDefinitivo
 *
 * @ORM\Table(name="texto_definitivo")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\TextoDefinitivoRepository")
 */
class TextoDefinitivo extends BaseClass {
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
	 * @ORM\Column(name="texto", type="text")
	 */
	private $texto;

	/**
	 * @ORM\Column(name="numero", type="string", length=255, nullable=true)
	 * @var string
	 */
	private $numero;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="MesaEntradaBundle\Entity\AnexoTextoDefinitivo", mappedBy="textoDefinitivo", cascade={"persist", "remove"}, orphanRemoval=true)
	 *
	 */
	private $anexos;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Dictamen", inversedBy="textosDefinitivos")
	 * @ORM\JoinColumn(name="dictamen_id", referencedColumnName="id")
	 */
	private $dictamen;

	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rama")
	 * @ORM\JoinColumn(name="rama_id", referencedColumnName="id", nullable=true)
	 */
	private $rama;

	/**
	 * @var
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\TextoDefinitivoExpedienteAdjunto", mappedBy="textoDefinitivo", cascade={"persist", "remove"}, orphanRemoval=true)
	 *
	 */
	private $expedientesAdjuntos;


	/**
	 * @var
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sesion")
	 * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
	 */
	private $aprobadoEnSesion;


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function __toString() {
		return $this->numero;
	}

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->anexos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->expedientesAdjuntos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set texto
     *
     * @param string $texto
     *
     * @return TextoDefinitivo
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return TextoDefinitivo
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return TextoDefinitivo
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
     * @return TextoDefinitivo
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Add anexo
     *
     * @param \MesaEntradaBundle\Entity\AnexoTextoDefinitivo $anexo
     *
     * @return TextoDefinitivo
     */
    public function addAnexo(\MesaEntradaBundle\Entity\AnexoTextoDefinitivo $anexo)
    {
	    $anexo->setTextoDefinitivo( $this );

	    $this->anexos->add( $anexo );

	    return $this;
    }

    /**
     * Remove anexo
     *
     * @param \MesaEntradaBundle\Entity\AnexoTextoDefinitivo $anexo
     */
    public function removeAnexo(\MesaEntradaBundle\Entity\AnexoTextoDefinitivo $anexo)
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

    /**
     * Set dictamen
     *
     * @param \MesaEntradaBundle\Entity\Dictamen $dictamen
     *
     * @return TextoDefinitivo
     */
    public function setDictamen(\MesaEntradaBundle\Entity\Dictamen $dictamen = null)
    {
        $this->dictamen = $dictamen;

        return $this;
    }

    /**
     * Get dictamen
     *
     * @return \MesaEntradaBundle\Entity\Dictamen
     */
    public function getDictamen()
    {
        return $this->dictamen;
    }

    /**
     * Set rama
     *
     * @param \AppBundle\Entity\Rama $rama
     *
     * @return TextoDefinitivo
     */
    public function setRama(\AppBundle\Entity\Rama $rama = null)
    {
        $this->rama = $rama;

        return $this;
    }

    /**
     * Get rama
     *
     * @return \AppBundle\Entity\Rama
     */
    public function getRama()
    {
        return $this->rama;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return TextoDefinitivo
     */
    public function setCreadoPor(\UsuariosBundle\Entity\Usuario $creadoPor = null)
    {
        $this->creadoPor = $creadoPor;

        return $this;
    }

    /**
     * Set actualizadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $actualizadoPor
     *
     * @return TextoDefinitivo
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Add expedientesAdjunto
     *
     * @param \AppBundle\Entity\TextoDefinitivoExpedienteAdjunto $expedientesAdjunto
     *
     * @return TextoDefinitivo
     */
    public function addExpedientesAdjunto(\AppBundle\Entity\TextoDefinitivoExpedienteAdjunto $expedientesAdjunto)
    {
	    $expedientesAdjunto->setTextoDefinitivo( $this );

	    $this->expedientesAdjuntos->add( $expedientesAdjunto );

	    return $this;
    }

    /**
     * Remove expedientesAdjunto
     *
     * @param \AppBundle\Entity\TextoDefinitivoExpedienteAdjunto $expedientesAdjunto
     */
    public function removeExpedientesAdjunto(\AppBundle\Entity\TextoDefinitivoExpedienteAdjunto $expedientesAdjunto)
    {
        $this->expedientesAdjuntos->removeElement($expedientesAdjunto);
    }

    /**
     * Get expedientesAdjuntos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExpedientesAdjuntos()
    {
        return $this->expedientesAdjuntos;
    }

    /**
     * Set aprobadoEnSesion
     *
     * @param \AppBundle\Entity\Sesion $aprobadoEnSesion
     *
     * @return TextoDefinitivo
     */
    public function setAprobadoEnSesion(\AppBundle\Entity\Sesion $aprobadoEnSesion = null)
    {
        $this->aprobadoEnSesion = $aprobadoEnSesion;

        return $this;
    }

    /**
     * Get aprobadoEnSesion
     *
     * @return \AppBundle\Entity\Sesion
     */
    public function getAprobadoEnSesion()
    {
        return $this->aprobadoEnSesion;
    }
}
