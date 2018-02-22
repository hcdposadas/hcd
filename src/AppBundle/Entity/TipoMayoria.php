<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * TipoMocion
 *
 * @ORM\Table(name="tipo_mayoria")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TipoMayoriaRepository")
 */
class TipoMayoria extends BaseClass
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string $funcion
     *
     * @ORM\Column(name="funcion", type="string", length=255)
     */
    private $funcion;

    public function __toString()
    {
        return $this->getNombre();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return TipoMayoria
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return string
     */
    public function getFuncion()
    {
        return $this->funcion;
    }

    /**
     * @param string $funcion
     */
    public function setFuncion($funcion)
    {
        $this->funcion = $funcion;
    }

    /**
     * @return array
     */
    public static function funciones()
    {
        return array(
            'mayoriaSimple'
        );
    }

    /**
     * @param Mocion $mocion
     * @return bool
     */
    public function mayoriaSimple(Mocion $mocion)
    {
        return ($mocion->getCuentaAfirmativos() > (intval($mocion->getCuentaTotal() / 2) / 2 + 1));
    }
}

