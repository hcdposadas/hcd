<?php

namespace MesaEntradaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UtilBundle\Entity\Base\BaseClass;

/**
 * LogExpediente
 *
 * @ORM\Table(name="log_expediente")
 * @ORM\Entity(repositoryClass="MesaEntradaBundle\Repository\LogExpedienteRepository")
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
     * @ORM\ManyToOne(targetEntity="MesaEntradaBundle\Entity\Expediente", inversedBy="logs")
     * @ORM\JoinColumn(name="expediente_id", referencedColumnName="id")
     */
    private $expediente;

    /**
     * @var string $log
     *
     * @ORM\Column(name="log", type="text")
     */
    private $log = '[]';

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
        $cambios[] = [
            'campo' => $campo,
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
}
