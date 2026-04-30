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
        $afirmativos = $mocion->getCuentaAfirmativos();
        $negativos = $mocion->getCuentaNegativos();
        $votosEmitidos = $afirmativos + $negativos;

        if ($votosEmitidos <= 0) {
            return false;
        }

        $afirmativosNecesarios = (int) floor($votosEmitidos / 2) + 1;

        if ($afirmativos >= $afirmativosNecesarios) {
            return true;
        }

        if ($afirmativos == $negativos) {
            ksort($this->ordenCargos);

            $votosOrdenados = $mocion->getVotos()->filter(function (Voto $voto) {
                // El desempate solo considera votos emitidos.
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

        return false;
    }
}
