<?php

namespace App\Service\Mayorias;

use App\Entity\Mocion;

/**
 * Class MayoriaCalificadaPresentes
 * @package App\Services\Mayorias
 *
 * Mayoría calificada presentes: dos tecios de los presentes.
 */
class MayoriaCalificadaPresentes extends Mayoria
{
    /**
     * @param Mocion $mocion
     * @return bool
     */
    public function seAprueba(Mocion $mocion)
    {
        if (array_key_exists($mocion->getCuentaTotal(), $this->cantidades)) {
            if ($mocion->getCuentaAfirmativos() >= $this->cantidades[$mocion->getCuentaTotal()]) {
                return true;
            }
        }
        return false;
    }
}
