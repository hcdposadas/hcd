<?php

namespace App\Entity;

use App\Entity\Sesion;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 */
class Log extends BaseClass
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $entityClass
     *
     * @ORM\Column(name="entity_class", type="string", length=255)
     */
    private $entityClass;

    /**
     * @var string $entityId
     *
     * @ORM\Column(name="entity_id", type="string", length=255)
     */
    private $entityId;

    /**
     * @var string $log
     *
     * @ORM\Column(name="log", type="text")
     */
    private $log = '{}';


    /**
     * @param $entity
     * @return Log
     */
    public static function forEntity($entity)
    {
        $log = new static();
        $log->setEntityClass(get_class($entity));
        $log->setEntityId($entity->getId());

        return $log;
    }

    public function __toString()
    {
        return 'Log#'.$this->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @param $campo
     * @param $valorOriginal
     * @param $valorNuevo
     */
    public function agregarCambio($campo, $valorOriginal, $valorNuevo)
    {
        $cambios = $this->getCambios();
        $cambios[$campo] = [
            'original' => $valorOriginal,
            'nuevo' => $valorNuevo,
        ];
        $this->setLog(json_encode($cambios));
    }

    /**
     * @return array
     */
    public function getCambios()
    {
        return json_decode($this->getLog(), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Log
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
     * @return Log
     */
    public function setFechaActualizacion($fechaActualizacion)
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * Set creadoPor
     *
     * @param \App\Entity\Usuario $creadoPor
     *
     * @return Log
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
     * @return Log
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     */
    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return bool
     */
    public function hasCambios()
    {
        return count($this->getCambios()) > 0;
    }
}
