<?php

namespace App\Service\Mayorias;

use App\Entity\Mocion;
use App\Entity\Persona;
use Doctrine\ORM\EntityManagerInterface;

abstract class Mayoria
{
    /**
     * @var array
     */
    protected $cantidades;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager, $cantidades)
    {
        $this->entityManager = $entityManager;
        if (!$cantidades) {
            $cantidades = '{}';
        }

        $this->cantidades = json_decode($cantidades, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @param Persona $persona
     * @param int|array $cargoId
     * @return bool
     */
    protected function personaTieneCargo(Persona $persona, $cargoId) : bool
    {
        if (!is_array($cargoId)) {
            $cargoId = [$cargoId];
        }

        $cargoPersona = $persona->getCargoPersona();
        foreach ($cargoPersona as $cp) {
            if (in_array($cp->getCargo()->getId(), $cargoId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Mocion $mocion
     * @return bool
     */
    public abstract function seAprueba(Mocion $mocion);
}