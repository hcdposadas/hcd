<?php

namespace App\Service\Mayorias;

use App\Entity\Mocion;
use App\Entity\Voto;

/**
 * Class MayoriaSimple
 * @package App\Services\Mayorias
 *
 * Mayoría simple: La mitad mas uno de los presentes.
 * En caso de empate, el resultado es del grupo en el que
 * se encuentre el concejal con cargo de mayor jerarquia.
 */
class MayoriaSimple extends Mayoria
{
    protected $ordenCargos = [
        0 => 23, // presidente
        1 => 21, // vicepresidente 1
        2 => 3, // vicepresidente 2
    ];

    /**
     * @param Mocion $mocion
     * @return bool
     */
    public function seAprueba(Mocion $mocion)
    {
        if (array_key_exists($mocion->getCuentaTotal(), $this->cantidades)) {
            $afirmativos = $mocion->getCuentaAfirmativos();
            $negativos = $mocion->getCuentaNegativos();

            // Afirmativos necesarios para aprobar
            if ($afirmativos >= $this->cantidades[$mocion->getCuentaTotal()]) {
                return true;
            }

            // En caso de empate (no hace falta verificar la cantidad total por el if de afuera)
            if ($afirmativos == $negativos) {
                ksort($this->ordenCargos);

                $votosOrdenados = $mocion->getVotos()->filter(function (Voto $voto) {
                    // quita las abstenciones
                    return !$voto->esAbstencion();
                })->map(function (Voto $voto) {
                    // obtiene los votos de los cargos con jerarquia a considerar, o null para los otros
                    $persona = $voto->getConcejal()->getPersona();
                    foreach ($this->ordenCargos as $jerarquia => $oc) {
                        if ($this->personaTieneCargo($persona, $oc)) {
                            return ['cargo' => $oc, 'jerarquia' => $jerarquia, 'voto' => $voto];
                        }
                    }
                    return null;
                })->filter(function ($x) {
                    // elimina los null, para que queden solo los cargos con jerarquia
                    return $x != null;
                })->toArray();

                // ordena los votos por jerarquia
                usort($votosOrdenados, function ($a, $b) {
                    return $a['jerarquia'] <=> $b['jerarquia'];
                });

                if (count($votosOrdenados)) {
                    return $votosOrdenados[0]['voto']->esAfirmativo();
                }
            }
        }

        return false;
    }
}
