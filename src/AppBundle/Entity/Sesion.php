<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * Sesion
 *
 * @ORM\Table(name="sesion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SesionRepository")
 */
class Sesion extends BaseClass
{
    const TIPO_ORDINARIA = 'sesion-tipo-ordinaria';
    const TIPO_EXTRAORDINARIA = 'sesion-tipo-extraordinaria';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Parametro $tipo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Parametro")
     */
    private $tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var \DateTime $fecha
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var ArrayCollection|Mocion[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mocion", mappedBy="sesion")
     */
    private $mociones;

    public function __construct()
    {
        $this->mociones = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $tipo = $this->tipo->getSlug() == self::TIPO_EXTRAORDINARIA ? 'Extraordinaria' : 'Ordinaria';
        return 'Sesión ' . $tipo. ' Nº'. $this->getNumero().' del '.$this->getFecha()->format('d/m/Y');
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
     * @return Parametro
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param Parametro $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Sesion
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return Mocion[]|ArrayCollection
     */
    public function getMociones()
    {
        return $this->mociones;
    }

    /**
     * @param Mocion[]|ArrayCollection $mociones
     */
    public function setMociones($mociones)
    {
        $this->mociones = $mociones;
    }
}
