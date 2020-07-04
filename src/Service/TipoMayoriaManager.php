<?php

namespace App\Service;

use App\Entity\Mocion;
use App\Service\Mayorias\Mayoria;
use App\Service\Mayorias\MayoriaCalificada;
use App\Service\Mayorias\MayoriaCalificadaPresentes;
use App\Service\Mayorias\MayoriaSimple;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class TipoMayoriaManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|string[]
     */
    public function tiposDeMayoria()
    {
        return array(
            'mayoriaSimple' => MayoriaSimple::class, // mitad mas uno
            'mayoriaCalificada' => MayoriaCalificada::class, // 10
            'mayoriaCalificadaPresentes' => MayoriaCalificadaPresentes::class, // 2/3 del cuerpo
        );
    }

    /**
     * @param Mocion $mocion
     * @return bool
     */
    public function seAprueba(Mocion $mocion)
    {
        $slug = $mocion->getTipoMayoria()->getSlug();
        $tipos = $this->tiposDeMayoria();
        if (!array_key_exists($slug, $tipos)) {
            throw new \RuntimeException('El tipo de mayoría '.$slug.' no es válido');
        }
        $clase = $tipos[$slug];

        /** @var Mayoria $mayoria */
        $mayoria = new $clase($this->entityManager, $mocion->getTipoMayoria()->getCantidades());

        return $mayoria->seAprueba($mocion);
    }
}
