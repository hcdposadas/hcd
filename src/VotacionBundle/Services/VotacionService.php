<?php

namespace VotacionBundle\Services;

use DateTime;
use Doctrine\ORM\EntityManager;
use UtilBundle\Services\NotificationsManager;
use VotacionBundle\Entity\Mocion;
use VotacionBundle\Entity\Votacion;

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

        $this->entityManager->persist($votacion);
        $this->entityManager->flush();

        $this->notificationManager->notify('votacion.abierta', [
            'id' => $votacion->getId(),
            'duracion' => $votacion->getDuracion(),
        ]);

        return $votacion;
    }
}