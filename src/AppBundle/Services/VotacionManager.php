<?php

namespace AppBundle\Services;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\Votacion;
use Doctrine\ORM\EntityManager;

class VotacionManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Mocion $mocion
     * @return Mocion
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function crear(Mocion $mocion)
    {
        $mocion->setNumero($this->getSiguienteNumero());
        $mocion->setEstado($this->getEstado(Mocion::ESTADO_PARA_VOTAR));
        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        return $mocion;
    }

    /**
     * @param Mocion $mocion
     * @return Votacion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function lanzar(Mocion $mocion)
    {
        if (!$mocion->puedeVotarse()) {
            throw new \RuntimeException('No se puede lanzar votación de la moción');
        }

        $mocion->setEstado($this->getEstado(Mocion::ESTADO_EN_VOTACION));
        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion(15);

        $this->entityManager->persist($votacion);
        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        return $votacion;
    }

    /**
     * @param Mocion $mocion
     * @return Votacion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function extender(Mocion $mocion)
    {
        if (!$mocion->enVotacion()) {
            throw new \RuntimeException('No se puede extender votación de la moción');
        }

        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion(10);

        $this->entityManager->persist($votacion);
        $this->entityManager->flush();

        return $votacion;
    }

    /**
     * @param Mocion $mocion
     * @return Mocion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finalizar(Mocion $mocion)
    {
        if (!$mocion->enVotacion()) {
            throw new \RuntimeException('No se puede finalizar la votación de la moción');
        }

        $mocion->setEstado($this->getEstado(Mocion::ESTADO_FINALIZADO));
        // TODO calcular totales

        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        return $mocion;
    }

    /**
     * @param $estado
     * @return Parametro|null
     */
    protected function getEstado($estado)
    {
        return $this->entityManager->getRepository(Parametro::class)->getBySlug($estado);
    }

    /**
     * @return int
     */
    protected function getSiguienteNumero()
    {
        return $this->entityManager->getRepository(Mocion::class)->siguienteNumero();
    }
}