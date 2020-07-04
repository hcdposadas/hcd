<?php

namespace App\Service\Mayorias;

use App\Entity\Mocion;

/**
 * Class MayoriaCalificada
 * @package App\Services\Mayorias
 *
 * Mayoría calificada: dos tercios del cuerpo.
 */
class MayoriaCalificada extends Mayoria
{
    /**
     * @param Mocion $mocion
     * @return bool
     */
    public function seAprueba(Mocion $mocion)
    {
        return $mocion->getCuentaAfirmativos() >= $this->cantidades['cantidad'];
    }
}
