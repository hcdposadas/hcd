<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * OrdenDelDia
 *
 * @ORM\Table(name="orden_del_dia")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenDelDiaRepository")
 */
class OrdenDelDia extends BaseClass
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
     * @var Sesion $sesion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sesion", inversedBy="od")
     * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
     */
    private $sesion;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\DictamenOD", mappedBy="ordenDelDia", cascade={"persist", "remove"})
     *
     */
    private $dictamenes;

    /**
     * @var bool
     *
     * @ORM\Column(name="cerrado", type="boolean")
     */
    private $cerrado;


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
     * Set cerrado
     *
     * @param boolean $cerrado
     *
     * @return OrdenDelDia
     */
    public function setCerrado($cerrado)
    {
        $this->cerrado = $cerrado;

        return $this;
    }

    /**
     * Get cerrado
     *
     * @return bool
     */
    public function getCerrado()
    {
        return $this->cerrado;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return OrdenDelDia
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
     * @return OrdenDelDia
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set sesion
     *
     * @param \AppBundle\Entity\Sesion $sesion
     *
     * @return OrdenDelDia
     */
    public function setSesion(\AppBundle\Entity\Sesion $sesion = null)
    {
        $this->sesion = $sesion;

        return $this;
    }

    /**
     * Get sesion
     *
     * @return \AppBundle\Entity\Sesion
     */
    public function getSesion()
    {
        return $this->sesion;
    }

    /**
     * Set creadoPor
     *
     * @param \UsuariosBundle\Entity\Usuario $creadoPor
     *
     * @return OrdenDelDia
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
     * @return OrdenDelDia
     */
    public function setActualizadoPor(\UsuariosBundle\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dictamenes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add dictamene
     *
     * @param \AppBundle\Entity\DictamenOD $dictamene
     *
     * @return OrdenDelDia
     */
    public function addDictamene(\AppBundle\Entity\DictamenOD $dictamene)
    {

        $dictamene->setOrdenDelDia($this);

        $this->dictamenes->add($dictamene);

        return $this;
    }

    /**
     * Remove dictamene
     *
     * @param \AppBundle\Entity\DictamenOD $dictamene
     */
    public function removeDictamene(\AppBundle\Entity\DictamenOD $dictamene)
    {
        $this->dictamenes->removeElement($dictamene);
    }

    /**
     * Get dictamenes
     *
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenes()
    {
        return $this->dictamenes;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeDeclaracion()
    {
        return $this->getDictamenes()->filter(function (DictamenOD $dod) {
            return $dod->getExpediente()
                && $dod->getExpediente()->getTipoProyecto()
                && $dod->getExpediente()->getTipoProyecto()->esTipoDeclaracion();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeComunicacion()
    {
        return $this->getDictamenes()->filter(function (DictamenOD $dod) {
            return $dod->getExpediente()
                && $dod->getExpediente()->getTipoProyecto()
                && $dod->getExpediente()->getTipoProyecto()->esTipoComunicacion();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeResolucion()
    {
        return $this->getDictamenes()->filter(function (DictamenOD $dod) {
            return $dod->getExpediente()
                && $dod->getExpediente()->getTipoProyecto()
                && $dod->getExpediente()->getTipoProyecto()->esTipoResolucion();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeOrdenanza()
    {
        return $this->getDictamenes()->filter(function (DictamenOD $dod) {
            return $dod->getExpediente()
                && $dod->getExpediente()->getTipoProyecto()
                && $dod->getExpediente()->getTipoProyecto()->esTipoOrdenanza();
        });
    }
}
