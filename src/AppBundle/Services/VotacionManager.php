<?php

namespace AppBundle\Services;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\Votacion;
use AppBundle\Entity\Voto;
use Doctrine\ORM\EntityManager;
use Exception;
use UsuariosBundle\Entity\Usuario;
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
     * @param Mocion $mocion
     * @param Usuario $usuario
     * @param $valorVoto
     * @return Voto
     * @throws Exception
     */
    public function votar(Mocion $mocion, Usuario $usuario, $valorVoto)
    {
        // TODO verificar que el usuario sea concejal

        if (!$mocion->enVotacion()) {
            throw new Exception('La moción no se encuentra en votación');
        }

        $votacion = null;
        foreach ($mocion->getVotaciones() as $votacion) {
            if (!$votacion->finalizada()) {
                break;
            }
        }

        if (!$votacion || $votacion->finalizada()) {
            throw new Exception('La moción no se encuentra en votación en este momento');
        }

        if (!in_array($valorVoto, array(Voto::VOTO_SI, Voto::VOTO_NO))) {
            throw new Exception('El valor del voto no es válido');
        }

        foreach ($mocion->getVotos() as $valorVoto) {
            if ($valorVoto->getCreadoPor()->getId() == $usuario->getId()) {
                throw new Exception('No se puede votar dos veces la misma moción');
            }
        }

        $voto = new Voto();
        $voto->setValor($valorVoto);
        $voto->setMocion($mocion);
        $voto->setVotacion($votacion);

        $this->entityManager->persist($voto);
        $this->entityManager->flush();

        return $voto;
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

        $textoMocion = '';
        if ($expediente = $mocion->getExpediente()) {
            $textoMocion = 'Expediente '.$expediente.': '.$expediente->getExtracto();
        }

        if ($tipoMayoria) {
            $tipoMayoria = 'Se aprueba con '.$tipoMayoria;
        }

        $this->notificationsManager->notify('votacion.abierta', array(
            'mocion' => 'Moción Nº'.$mocion->__toString(),
            'textoMocion' => $textoMocion,
            'tipoMayoria' => $tipoMayoria,
            'sesion' => $mocion->getSesion(),
            'duracion' => $duracion
        ));

        $this->notificationsManager->notify('votacion.cerrada', null, $duracion);
    }
}