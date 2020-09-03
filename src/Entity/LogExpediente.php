<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\BaseClass;

/**
 * LogExpediente
 *
 * @ORM\Table(name="log_expediente")
 * @ORM\Entity(repositoryClass="App\Repository\LogExpedienteRepository")
 */
class LogExpediente extends BaseClass
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
     * @var Expediente $expediente
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Expediente")
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id", nullable=true)
     */
    private $expediente;

	/**
	 * @var Sesion $sesion
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Sesion")
	 * @ORM\JoinColumn(name="sesion_id", referencedColumnName="id", nullable=true)
	 */
	private $sesion;

    /**
     * @var string $log
     *
     * @ORM\Column(name="log", type="text")
     */
    private $log = '{}';

    public function __toString()
    {
        return 'LogExpediente#'.$this->getId();
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
     * @return Expediente
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * @param Expediente $expediente
     */
    public function setExpediente($expediente)
    {
        $this->expediente = $expediente;
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
     * @return LogExpediente
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
     * @return LogExpediente
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
     * @return LogExpediente
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
     * @return LogExpediente
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
     * @return LogExpediente
     */
    public function setActualizadoPor(\App\Entity\Usuario $actualizadoPor = null)
    {
        $this->actualizadoPor = $actualizadoPor;

        return $this;
    }
}
