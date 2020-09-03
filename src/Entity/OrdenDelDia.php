<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Dictamen;
use App\Entity\Expediente;
use App\Entity\Base\BaseClass;

/**
 * OrdenDelDia
 *
 * @ORM\Table(name="orden_del_dia")
 * @ORM\Entity(repositoryClass="App\Repository\OrdenDelDiaRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Sesion", inversedBy="od")
     * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
     */
    private $sesion;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\Entity\DictamenOD", mappedBy="ordenDelDia", cascade={"persist", "remove"})
     *
     */
    private $dictamenes;

    /**
     * @var bool
     *
     * @ORM\Column(name="cerrado", type="boolean")
     */
    private $cerrado;


	public function __toString():string {
		return $this->sesion->__toString() . ' - ' . $this->sesion->getFecha()->format( 'd/m/Y' );
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
     * @param \App\Entity\Sesion $sesion
     *
     * @return OrdenDelDia
     */
    public function setSesion(\App\Entity\Sesion $sesion = null)
    {
        $this->sesion = $sesion;

        return $this;
    }

    /**
     * Get sesion
     *
     * @return \App\Entity\Sesion
     */
    public function getSesion()
    {
        return $this->sesion;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return OrdenDelDia
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
     * @return OrdenDelDia
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
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
     * @param \App\Entity\DictamenOD $dictamene
     *
     * @return OrdenDelDia
     */
    public function addDictamene(\App\Entity\DictamenOD $dictamene)
    {

        $dictamene->setOrdenDelDia($this);

        $this->dictamenes->add($dictamene);

        return $this;
    }

    /**
     * Remove dictamene
     *
     * @param \App\Entity\DictamenOD $dictamene
     */
    public function removeDictamene(\App\Entity\DictamenOD $dictamene)
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
    public function getDictamenesConTratamientoPreferencial()
    {
        return $this->ordenarProyectos(
            $this->getDictamenes()->filter(function (DictamenOD $dod) {
                $dictamen = $dod->getDictamen();
                if (!$dictamen) {
                    return false;
                }

                return $dictamen->getTipoProyecto()
                    && $dod->getTieneTratamientoPreferencial();
            })
        );
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeDeclaracion()
    {
        return $this->ordenarProyectos(
            $this->getDictamenes()->filter(function (DictamenOD $dod) {
                $dictamen = $dod->getDictamen();
                if (!$dictamen) {
                    return false;
                }

                return $dictamen->getTipoProyecto()
                    && $dictamen->getTipoProyecto()->esTipoDeclaracion()
                       && !$dod->getTieneTratamientoPreferencial();
            })
        );
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeComunicacion()
    {
        return $this->ordenarProyectos(
            $this->getDictamenes()->filter(function (DictamenOD $dod) {
                $dictamen = $dod->getDictamen();
                if (!$dictamen) {
                    return false;
                }

                return $dictamen->getTipoProyecto()
                    && $dictamen->getTipoProyecto()->esTipoComunicacion()
                       && !$dod->getTieneTratamientoPreferencial();
            })
        );
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeResolucion()
    {
        return $this->ordenarProyectos(
            $this->getDictamenes()->filter(function (DictamenOD $dod) {
                $dictamen = $dod->getDictamen();
                if (!$dictamen) {
                    return false;
                }

                return $dictamen->getTipoProyecto()
                    && $dictamen->getTipoProyecto()->esTipoResolucion()
                       && !$dod->getTieneTratamientoPreferencial();
            })
        );
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    public function getDictamenesDeOrdenanza()
    {
        return $this->ordenarProyectos(
            $this->getDictamenes()->filter(function (DictamenOD $dod) {
                $dictamen = $dod->getDictamen();
                if (!$dictamen) {
                    return false;
                }

                return $dictamen->getTipoProyecto()
                    && $dictamen->getTipoProyecto()->esTipoOrdenanza()
                       && !$dod->getTieneTratamientoPreferencial();
            })
        );
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|DictamenOD[] $proyectos
     * @return \Doctrine\Common\Collections\Collection|DictamenOD[]
     */
    private function ordenarProyectos($proyectos)
    {
        $iterator = $proyectos->getIterator();

        $iterator->uasort(function (DictamenOD $a, DictamenOD $b) {

            // Del expediente de ambos proyectos, separa el numero y el año
            // Pueden ser numero-letra-anio o numero-numero-letra-anio

            $expA = $a->getDictamen()->getExpediente()->__toString();
            $expB = $b->getDictamen()->getExpediente()->__toString();

            if (substr_count($expA, '-') == 3) {
                list( $numeroa, $numero2a, $letraa, $anioa ) = explode( '-', $expA, 4 );
                $numeroa = $numeroa.$numero2a;
            } else {
                list( $numeroa, $letraa, $anioa ) = explode( '-', $expA, 3 );
            }

            if (substr_count($expB, '-') == 3) {
                list( $numerob, $numero2b, $letrab, $aniob ) = explode( '-', $expB, 4 );
                $numerob = $numerob.$numero2b;
            } else {
                list( $numerob, $letrab, $aniob ) = explode( '-', $expB, 3 );
            }

            // Compara por anio, y si son iguales, por numero

            if ($anioa < $aniob){
                return -1;
            } elseif ($anioa > $aniob) {
                return 1;
            } else {
                if ($numeroa < $numerob) {
                    return -1;
                } elseif ($numeroa > $numerob) {
                    return 1;
                } else {
                    return 0;
                }
            }
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }
}
