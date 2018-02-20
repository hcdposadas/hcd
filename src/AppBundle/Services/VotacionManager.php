<?php

namespace AppBundle\Services;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\Votacion;
use Doctrine\ORM\EntityManager;
use UtilBundle\Services\NotificationsManager;

class VotacionManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var NotificationsManager
     */
    protected $notificationsManager;

    public function __construct(EntityManager $entityManager, NotificationsManager $notificationsManager)
    {
        $this->entityManager = $entityManager;
        $this->notificationsManager = $notificationsManager;
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
            if ($mocion->enVotacion()) {
                throw new \RuntimeException('No se puede lanzar la votación porque la moción ya se encuentra en votación');
            } elseif ($mocion->finalizada()) {
                throw new \RuntimeException('No se puede lanzar la votación porque la moción ya fue finalizada');
            } else {
                throw new \RuntimeException('No se puede lanzar votación');
            }
        }

        $enVotacion = $this->entityManager->getRepository(Mocion::class)->getEnVotacion();
        if ($enVotacion) {
            throw new \RuntimeException('No se puede lanzar la votación porque la Moción Nº'.$enVotacion.' se encuentra en votación.');
        }

        $duracion = 15;

        $mocion->setEstado($this->getEstado(Mocion::ESTADO_EN_VOTACION));
        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion($duracion);

        $this->entityManager->persist($votacion);
        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        $this->notificar($mocion, $duracion);

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

        $duracion = 10;

        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion($duracion);

        $this->entityManager->persist($votacion);
        $this->entityManager->flush();

        $this->notificar($mocion, $duracion);

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

    /**
     * @param Mocion $mocion
     * @param $duracion
     */
    protected function notificar(Mocion $mocion, $duracion)
    {
        $tipoMayoria = $mocion->getTipoMayoria();
        $tipoMayoria = $tipoMayoria ? $tipoMayoria->__toString() : null;

        $this->notificationsManager->notify('votacion.abierta', array(
            'mocion' => $mocion->__toString(),
            'tipoMayoria' => $tipoMayoria,
            'sesion' => $mocion->getSesion(),
            'duracion' => $duracion
        ));

        $this->notificationsManager->notify('votacion.cerrada', null, $duracion);
    }
}