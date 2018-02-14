<?php

namespace VotacionBundle\Services;

use DateTime;
use Doctrine\ORM\EntityManager;
use UtilBundle\Services\NotificationsManager;
use VotacionBundle\Entity\Mocion;
use VotacionBundle\Entity\Votacion;
use VotacionBundle\Entity\Voto;

class VotacionService
{
    /**
     * @var EntityManager $entityManager
     */
    protected $entityManager;

    /**
     * @var NotificationsManager
     */
    protected $notificationManager;

    public function __construct(EntityManager $entityManager, NotificationsManager $notificationsManager)
    {
        $this->entityManager = $entityManager;
        $this->notificationManager = $notificationsManager;
    }

    /**
     * @param Mocion $mocion
     * @param $duracion
     * @return Votacion
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function nuevaVotacion(Mocion $mocion, $duracion)
    {
        $desde = new DateTime();
        $hasta = clone($desde);
        $hasta->modify('+'.intval($duracion).' seconds');

        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDesde($desde);
        $votacion->setHasta($hasta);
        $votacion->setDuracion($duracion);

        $mocion->setEstado(Mocion::ESTADO_EN_VOTACION);

        $this->entityManager->persist($mocion);
        $this->entityManager->persist($votacion);
        $this->entityManager->flush();

        $this->notificationManager->notify('votacion.abierta', [
            'id' => $votacion->getId(),
            'duracion' => $votacion->getDuracion(),
            'mocion' => $votacion->getMocion()->__toString(),
        ]);

        return $votacion;
    }

    public function finalizarVotacion(Votacion $votacion)
    {
        $mocion = $votacion->getMocion();
        $mocion->setEstado(Mocion::ESTADO_FINALIZADO);

        $cuentaAfirmativos = 0;
        $cuentaNegtivos = 0;
        $cuentaAbstenciones = 0;

        /** @var Votacion $votacion */
        foreach ($mocion->getVotaciones() as $votacion) {
            /** @var Voto $voto */
            foreach ($votacion->getVotos() as $voto) {
                if ($voto->esAfirmativo()) {
                    $cuentaAfirmativos++;
                } elseif ($voto->esNegativo()) {
                    $cuentaNegtivos++;
                } elseif ($voto->esAbstencion()) {
                    $cuentaAbstenciones++;
                }
            }
        }

        $mocion->setCuentaAfirmativos($cuentaAfirmativos);
        $mocion->setCuentaNegativos($cuentaNegtivos);
        $mocion->setCuentaAbstenciones($cuentaAbstenciones);

        $this->entityManager->persist($mocion);
        $this->entityManager->flush();
    }
}